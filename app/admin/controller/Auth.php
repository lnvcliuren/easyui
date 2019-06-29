<?php

    namespace app\admin\controller;
    use app\admin\controller\Base;
    use app\admin\model\Auth as AuthModel;
    use app\admin\model\Menu;

    class Auth extends Base
    {

        public function index(){
            $auth = new AuthModel();
            // 获取一级
            $list = $auth->getAuths($this->request->param('keywords'));
            $this->assign('list',$list);
            $this->assign('totalcounts',AuthModel::count());
            return $this->fetch();
        }

        public function add(){
            $mid = $this->request->param('mid');
            $plist = AuthModel::where('pid',0)->where('menu_id',$mid)->select();
            foreach($plist as $k=>$v){
                $plist[$k]['children'] = AuthModel::where('pid',$v['id'])->select();
            }
            $this->assign('plist',$plist);
            $this->assign('menus',Menu::all());
            if($mid){
                return json_encode($plist);
            }else{
                return $this->fetch();
            }
        }

        public function adddo(){
            $data = array(
                'title' => $this->request->param('title'),
                'is_menu' => $this->request->param('is_menu')?$this->request->param('is_menu'):0,
                'pid' => $this->request->param('pid'),
                'auth' => $this->request->param('auth'),
                'menu_id' => $this->request->param('menu_id'),
                'icon_code' => $this->request->param('icon_code'),
            );
            if(count($data)>=3){
                if($data['pid']==0){
                    $check = AuthModel::get(['title'=>$data['title']]);
                }else{
                    $check = AuthModel::where('auth',$data['auth'])->find(); 
                }
                // 检测是否重复              
                if($check){
                    $res = ['statusCode'=>300,'message'=>'规则已存在'];
                }else{ 
                    if($insert = AuthModel::create($data)){
                        $res = ['statusCode'=>200,'message'=>'操作成功'];
                    }else{
                        $res = ['statusCode'=>300,'message'=>'操作失败'];
                    }
                }  
                                         
            }else{
                $res = ['statusCode'=>300,'message'=>'数据录入错误'];
            }
            
            echo json_encode($res);
        }

        public function edit(){
            $plist = AuthModel::where('pid',0)->select();
            $auth = AuthModel::get($this->request->param('id'));
            foreach($plist as $k=>$v){
                $plist[$k]['children'] = AuthModel::where('pid',$v['id'])->select();
            }
            $this->assign('plist',$plist);
            $this->assign('menus',Menu::all());          
            $this->assign('info',$auth);
            
            return $this->fetch();
        }

        public function editdo(){
            $id = $this->request->param('id');
            $data = array(
                'id' => (int)$id,
                'title' => $this->request->param('title'),
                'is_menu' => $this->request->param('is_menu')?$this->request->param('is_menu'):0,
                'pid' => $this->request->param('pid'),
                'auth' => $this->request->param('auth'),
                'menu_id' => $this->request->param('menu_id'),
                'icon_code' => $this->request->param('icon_code'),
            );
            if(count($data)>=3 and $id){
                if($data['pid']==0){
                    $check = AuthModel::where('title',$data['title'])->where('id','<>',$id)->select();
                }else{
                    $check = AuthModel::where('auth',$data['auth'])->where('id','<>',$id)->select();
                }                
                if($check){
                    $res = ['statusCode'=>300,'message'=>'规则已存在'];
                }else{
                    if($update = AuthModel::update($data)){
                        $res = ['statusCode'=>200,'message'=>'操作成功'];
                    }else{
                        $res = ['statusCode'=>300,'message'=>'操作失败'];
                    } 
                }                            
            }else{
                $res = ['statusCode'=>300,'message'=>'数据录入错误'];
            }
            
            echo json_encode($res);
        }

        public function delete(){
            $ids = explode(',',$this->request->param('ids'));
            if(count($ids)>0){
                foreach($ids as $key=>$value){
                    AuthModel::destroy($value);
                }
                $res = ['statusCode'=>200,'message'=>'操作完成'];
            }else{
                $res = ['statusCode'=>300,'message'=>'操作失败'];
            }
            echo json_encode($res);
        }

        

    }