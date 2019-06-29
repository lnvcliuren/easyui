<?php

    namespace app\admin\controller; 
    use app\admin\controller\Base;
    use app\admin\model\Xmfl as XmflModel;

    class Xmfl extends Base
    {
        public function index(){
            $xmfl = new XmflModel();
            // 获取数据
            $list = XmflModel::where('title','like','%'.$this->request->param('keywords').'%')
                            ->field('id,title as name,pid,status')
                            ->select();
            
            $this->assign('list',json_encode($list));
            $this->assign('totalcounts',XmflModel::count());

            //echo json_encode($xmfl->getAuths($list,0,0));
            return $this->fetch('jcsz/Xmfl/index');
        }

        public function add(){
            return $this->fetch('jcsz/Xmfl/add');
        }

        public function adddo(){
            $data = array(
                'title' => $this->request->param('title'),
            );
            if(count($data)>=1){
                $check = XmflModel::get(['title'=>$data['title']]);
                // 检测是否重复              
                if($check){
                    $res = ['statusCode'=>300,'message'=>'房间类别已存在'];
                }else{ 
                    if($insert = XmflModel::create($data)){
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
            $this->assign('info',XmflModel::get($this->request->param('id')));
            return $this->fetch('jcsz/Xmfl/edit');
        }

        public function editdo(){
            $data = array(
                'id' => $this->request->param('id'),
                'title' => $this->request->param('title'),
            );
            $son_title = $this->request->param('son_title');
            if(count($data)>=2){
                $check = XmflModel::where('title',$data['title'])->where('id','<>',$data['id'])->find();
                // 检测是否重复              
                if($check){
                    $res = ['statusCode'=>300,'message'=>'房间类别已存在'];
                }else{ 
                    if($update = XmflModel::update($data)){
                        if($son_title){
                            XmflModel::create(['pid'=>$data['id'],'title'=>$son_title]);
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
                    XmflModel::destroy(['pid' => $value]);
                    XmflModel::destroy($value);
                }
                $res = ['statusCode'=>200,'message'=>'操作完成'];
            }else{
                $res = ['statusCode'=>300,'message'=>'操作失败'];
            }
            echo json_encode($res);
        }


        

        


    }
