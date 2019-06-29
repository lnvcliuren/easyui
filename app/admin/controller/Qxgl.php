<?php

    namespace app\admin\controller; 
    use app\admin\controller\Base;
    use app\admin\model\Qxgl as QxglModel;

    class Qxgl extends Base
    {
        public function index(){
            // 获取数据
            $list = QxglModel::where('title','like','%'.$this->request->param('keywords').'%')  
                            ->select(); 
            
            $this->assign('list',$list);
            $this->assign('totalcounts',QxglModel::count());

            return $this->fetch('qtzd/Qxgl/index');
        }

        public function add(){
            return $this->fetch('qtzd/Qxgl/add');
        }

        public function adddo(){
            $data = array(
                'title' => $this->request->param('title'),
            );
            if(count($data)>=1){
                $check = QxglModel::get(['title'=>$data['title']]);
                // 检测是否重复              
                if($check){
                    $res = ['statusCode'=>300,'message'=>'区县已存在'];
                }else{ 
                    if($insert = QxglModel::create($data)){
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
            $this->assign('info',QxglModel::get($this->request->param('id')));
            return $this->fetch('qtzd/Qxgl/edit');
        }

        public function editdo(){
            $data = array(
                'id' => $this->request->param('id'),
                'title' => $this->request->param('title'),
            );
            if(count($data)>=2){
                $check = QxglModel::where('title',$data['title'])->where('id','<>',$data['id'])->find();
                // 检测是否重复              
                if($check){
                    $res = ['statusCode'=>300,'message'=>'区县已存在'];
                }else{ 
                    if($update = QxglModel::update($data)){
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
                    QxglModel::destroy($value);
                }
                $res = ['statusCode'=>200,'message'=>'操作完成'];
            }else{
                $res = ['statusCode'=>300,'message'=>'操作失败'];
            }
            echo json_encode($res);
        }


        

        


    }
