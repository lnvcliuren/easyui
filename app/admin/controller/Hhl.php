<?php

    namespace app\admin\controller; 
    use app\admin\controller\Base;
    use app\admin\model\Hhl as HhlModel;
    use think\Db;

    class Hhl extends Base
    {
        public function index(){
            // 获取数据
            $list = HhlModel::where('title','like','%'.$this->request->param('keywords').'%')  
                            ->select(); 
            
            $this->assign('list',$list);
            $this->assign('totalcounts',HhlModel::count());

            return $this->fetch('base/Hhl/index');
        }

        public function add(){
            return $this->fetch('base/Hhl/add');
        }

        public function adddo(){
            $data = array(
                'title' => $this->request->param('title'),
                'price' => $this->request->param('price'),
                'ls' => $this->request->param('ls'),
                'zs' => $this->request->param('zs'),
            );
            if(count($data)>=4){
                $check = HhlModel::get(['title'=>$data['title']]);
                // 检测是否重复              
                if($check){
                    $res = ['statusCode'=>300,'message'=>'火化炉已存在'];
                }else{ 
                    if($insert = HhlModel::create($data)){
                        $data_hhllc = array();// 存储炉次
                        for($i=0;$i<$data['zs'];$i++){// 循环组数
                            for($j=0;$j<$data['ls'];$j++){
                                $data_hhllc = [ 'hhlid'=>$insert->id,'hhllc'=>($i+1) ];
                                Db::name('hhllc')->insert($data_hhllc);
                            }
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

        public function edit(){
            $this->assign('info',HhlModel::get($this->request->param('id')));
            return $this->fetch('base/Hhl/edit');
        }

        public function editdo(){
            $data = array(
                'id' => $this->request->param('id'),
                'title' => $this->request->param('title'),
                'price' => $this->request->param('price'),
                'ls' => $this->request->param('ls'),
                'zs' => $this->request->param('zs'),
            );
            if(count($data)>=5){
                $check = HhlModel::where('title',$data['title'])->where('id','<>',$data['id'])->find();
                // 检测是否重复              
                if($check){
                    $res = ['statusCode'=>300,'message'=>'火化炉已存在'];
                }else{ 
                    if($update = HhlModel::update($data)){
                        Db::name('hhllc')->where('id',$data['id'])->delete();
                        $data_hhllc = array();// 存储炉次
                        for($i=0;$i<$data['zs'];$i++){// 循环组数
                            for($j=0;$j<$data['ls'];$j++){
                                $data_hhllc = [ 'hhlid'=>$update->id,'hhllc'=>($i+1) ];
                                Db::name('hhllc')->insert($data_hhllc);
                            }
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
                    HhlModel::destroy($value);
                    Db::name('hhllc')->where('id',$value)->delete();
                }
                $res = ['statusCode'=>200,'message'=>'操作完成'];
            }else{
                $res = ['statusCode'=>300,'message'=>'操作失败'];
            }
            echo json_encode($res);
        }


        

        


    }
