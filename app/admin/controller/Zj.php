<?php

    namespace app\admin\controller; 
    use app\admin\controller\Base;
    use app\admin\model\Zj as ZjModel;

    class Zj extends Base
    {
        public function index(){
            // 获取数据
            $list = ZjModel::where('title','like','%'.$this->request->param('keywords').'%')  
                            ->select(); 
            
            $this->assign('list',$list);
            $this->assign('totalcounts',ZjModel::count());

            return $this->fetch('qtzd/Zj/index');
        }

        public function add(){
            return $this->fetch('qtzd/Zj/add');
        }

        public function adddo(){
            $data = array(
                'title' => $this->request->param('title'),
            );
            if(count($data)>=1){
                $check = ZjModel::get(['title'=>$data['title']]);
                // 检测是否重复              
                if($check){
                    $res = ['statusCode'=>300,'message'=>'遗体运送方式已存在'];
                }else{ 
                    if($insert = ZjModel::create($data)){
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
            $this->assign('info',ZjModel::get($this->request->param('id')));
            return $this->fetch('qtzd/Zj/edit');
        }

        public function editdo(){
            $data = array(
                'id' => $this->request->param('id'),
                'title' => $this->request->param('title'),
            );
            if(count($data)>=2){
                $check = ZjModel::where('title',$data['title'])->where('id','<>',$data['id'])->find();
                // 检测是否重复              
                if($check){
                    $res = ['statusCode'=>300,'message'=>'遗体运送方式已存在'];
                }else{ 
                    if($update = ZjModel::update($data)){
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
                    ZjModel::destroy($value);
                }
                $res = ['statusCode'=>200,'message'=>'操作完成'];
            }else{
                $res = ['statusCode'=>300,'message'=>'操作失败'];
            }
            echo json_encode($res);
        }


        

        


    }
