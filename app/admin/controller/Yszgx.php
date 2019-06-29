<?php

    namespace app\admin\controller; 
    use app\admin\controller\Base;
    use app\admin\model\Yszgx as YszgxModel;

    class Yszgx extends Base
    {
        public function index(){
            // 获取数据
            $list = YszgxModel::where('title','like','%'.$this->request->param('keywords').'%')  
                            ->select(); 
            
            $this->assign('list',$list);
            $this->assign('totalcounts',YszgxModel::count());

            return $this->fetch('base/Yszgx/index');
        }

        public function add(){
            return $this->fetch('base/Yszgx/add');
        }

        public function adddo(){
            $data = array(
                'title' => $this->request->param('title'),
            );
            if(count($data)>=1){
                $check = YszgxModel::get(['title'=>$data['title']]);
                // 检测是否重复              
                if($check){
                    $res = ['statusCode'=>300,'message'=>'与死者关系已存在'];
                }else{ 
                    if($insert = YszgxModel::create($data)){
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
            $this->assign('info',YszgxModel::get($this->request->param('id')));
            return $this->fetch('base/Yszgx/edit');
        }

        public function editdo(){
            $data = array(
                'id' => $this->request->param('id'),
                'title' => $this->request->param('title'),
            );
            if(count($data)>=2){
                $check = YszgxModel::where('title',$data['title'])->where('id','<>',$data['id'])->find();
                // 检测是否重复              
                if($check){
                    $res = ['statusCode'=>300,'message'=>'与死者关系已存在'];
                }else{ 
                    if($update = YszgxModel::update($data)){
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
                    YszgxModel::destroy($value);
                }
                $res = ['statusCode'=>200,'message'=>'操作完成'];
            }else{
                $res = ['statusCode'=>300,'message'=>'操作失败'];
            }
            echo json_encode($res);
        }


        

        


    }
