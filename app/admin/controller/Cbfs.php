<?php

    namespace app\admin\controller; 
    use app\admin\controller\Base;
    use app\admin\model\Cbfs as CbfsModel;

    class Cbfs extends Base
    {
        public function index(){
            // 获取数据
            $list = CbfsModel::where('title','like','%'.$this->request->param('keywords').'%')  
                            ->select(); 
            
            $this->assign('list',$list);
            $this->assign('totalcounts',CbfsModel::count());

            return $this->fetch('base/Cbfs/index');
        }

        public function add(){
            return $this->fetch('base/Cbfs/add');
        }

        public function adddo(){
            $data = array(
                'title' => $this->request->param('title'),
                'price' => $this->request->param('price'),
            );
            if(count($data)>=2){
                $check = CbfsModel::get(['title'=>$data['title']]);
                // 检测是否重复              
                if($check){
                    $res = ['statusCode'=>300,'message'=>'出殡方式已存在'];
                }else{ 
                    if($insert = CbfsModel::create($data)){
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
            $this->assign('info',CbfsModel::get($this->request->param('id')));
            return $this->fetch('base/Cbfs/edit');
        }

        public function editdo(){
            $data = array(
                'id' => $this->request->param('id'),
                'title' => $this->request->param('title'),
                'price' => $this->request->param('price'),
            );
            if(count($data)>=3){
                $check = CbfsModel::where('title',$data['title'])->where('id','<>',$data['id'])->find();
                // 检测是否重复              
                if($check){
                    $res = ['statusCode'=>300,'message'=>'出殡方式已存在'];
                }else{ 
                    if($update = CbfsModel::update($data)){
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
                    CbfsModel::destroy($value);
                }
                $res = ['statusCode'=>200,'message'=>'操作完成'];
            }else{
                $res = ['statusCode'=>300,'message'=>'操作失败'];
            }
            echo json_encode($res);
        }


        

        


    }
