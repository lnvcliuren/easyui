<?php

    namespace app\admin\controller;
    use app\admin\controller\Base;
    use app\admin\model\Role as RoleModel;
    use think\Request;
    use app\admin\model\Auth as AuthModel;

    class Role extends Base
    {
        public function index(){
            // 获取总数
            $totalCounts = RoleModel::where('name','like','%'.$this->request->param('keywords').'%')
                            ->count();

            // 获取数据
            $list = RoleModel::where('pid',0)
                    ->where('name','like','%'.$this->request->param('keywords').'%')
                    ->select();               
            foreach($list as $k=>$v){
                $list[$k]['childrens'] = RoleModel::where('pid',$v['id'])->select();   
            }
            
            $this->assign('list',$list);
            $this->assign('totalcounts',$totalCounts);
            return $this->fetch();
        }

        public function add(){
            $this->assign('plist',RoleModel::where('pid',0)->where('status',1)->select());
            return $this->fetch();
        }

        public function adddo(){
            $data = array(
                'name' => $this->request->param('name'),
                'status' => $this->request->param('status')?$this->request->param('status'):0,
                'pid' => $this->request->param('pid'),
            );
            if(count($data)>=3){
                $check = RoleModel::get(['name'=>$data['name'],'pid'=>$data['pid']]);
                if($check){
                    $res = ['statusCode'=>300,'message'=>'角色已存在'];
                }else{
                    if($insert = RoleModel::create($data)){
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
            $this->assign('info',RoleModel::get($this->request->param('id')));
            $this->assign('plist',RoleModel::where('pid',0)->where('status',1)->select());
            return $this->fetch();
        }

        public function editdo(){
            $data = array(
                'id' => $this->request->param('id'),
                'name' => $this->request->param('name'),
                'status' => $this->request->param('status')?$this->request->param('status'):0,
                'pid' => $this->request->param('pid'),
            );
            if(count($data)>=4){
                $check = RoleModel::where('name',$data['name'])->where('pid',$data['pid'])->where('id','<>',$data['id'])->select();
                if($check){
                    $res = ['statusCode'=>300,'message'=>'角色已存在'];
                }else{
                    if($update = RoleModel::update($data)){
                        if( $data['status']=='0' ){
                            // 禁用情况下，将下级角色都禁用，如果存在
                            RoleModel::where('pid',$data['id'])
                                        ->update(['status'=>0]);
                        }
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
                    if($value!=='1')
                        RoleModel::destroy($value);
                }
                $res = ['statusCode'=>200,'message'=>'操作完成'];
            }else{
                $res = ['statusCode'=>300,'message'=>'操作失败'];
            }

            echo json_encode($res);
        }

        public function set(){
            // 获取所有顶级规则
            $auth = new AuthModel();
            $authdatas = $auth->getAuths('');
            // 获取角色已经设置的规则
            $role = RoleModel::get($this->request->param('id'));
            $auths = $role->auths()->select();
            // 将角色已经设置的规则id转化为字符串
            $authids = array();
            if($auths){                
                foreach($auths as $k=>$auth){
                    $authids[$k] = $auth['id'];
                }
            }           

            $this->assign('id',$this->request->param('id'));
            $this->assign('role',$role->name);
            $this->assign('list',$authdatas);
            $this->assign('authids',$authids);  

            return $this->fetch();
        }

        public function setdo(){
            // 存储二级角色
            $childrens = array();

            $ids = isset($_POST['set'])?$_POST['set']:array();
            $role = RoleModel::get($this->request->param('id'));

            // 检测是否顶级角色，是：检索所有的属下二级角色
            if($role->pid==0){
                $childrens = RoleModel::get(['pid'=>$role->id]);
            }
   
            if(count($ids)>0){
                $role->auths()->detach();
                foreach($ids as $v){
                    $auths = AuthModel::get($v);
                    if($childrens){
                        $childrens->auths()->detach($auths);
                        $childrens->auths()->attach($auths);
                    }
                    $role->auths()->detach($auths);
                    $role->auths()->attach($auths);
                }
            }else{
                if($childrens){
                    $childrens->auths()->detach();
                }
                $role->auths()->detach();
            }
  
            $res = ['statusCode'=>200,'message'=>'操作成功'];
            echo json_encode($res);
        }

    }