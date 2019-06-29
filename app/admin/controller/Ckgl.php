<?php
    /*
     *  仓库操作类
     */
    namespace app\admin\controller; 
    use app\admin\controller\Base;
    use app\admin\model\Ckgl as CkglModel;

    class Ckgl extends Base
    {
        public function index(){
            $ckgl = new CkglModel();
            // 获取一级数据
            $list = $ckgl->getAuths($this->request->param('keywords'));

            $this->assign('list',$list);
            $this->assign('totalcounts',CkglModel::count());

            return $this->fetch('jcsz/Ckgl/index');
        }

        public function add(){
            $plist = CkglModel::where('pid',0)->select();
            $this->assign('plist',$plist);
            return $this->fetch('jcsz/Ckgl/add');
        }

        public function adddo(){
            $data = array(
                'title' => $this->request->param('title'),
                'pid' => $this->request->param('pid'),
                'lxr' => $this->request->param('lxr'),
                'phone' => $this->request->param('phone'),
            );
            if(count($data)>=2){
                $cheCkgl = CkglModel::get(['title'=>$data['title'],'pid'=>$data['pid']]);
                // 检测是否重复              
                if($cheCkgl){
                    $res = ['statusCode'=>300,'message'=>'仓库信息已存在'];
                }else{ 
                    if($insert = CkglModel::create($data)){
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
            $plist = CkglModel::where('pid',0)->select();
            $this->assign('plist',$plist);
            $this->assign('info',CkglModel::get($this->request->param('id')));
            return $this->fetch('jcsz/Ckgl/edit');
        }

        public function editdo(){
            $data = array(
                'id' => $this->request->param('id'),
                'title' => $this->request->param('title'),
                'pid' => $this->request->param('pid'),
                'lxr' => $this->request->param('lxr'),
                'phone' => $this->request->param('phone'),
            );
            if(count($data)>=3){
                $cheCkgl = CkglModel::where(['title'=>$data['title'],'pid'=>$data['pid']])->where('id','<>',$data['id'])->find();
                // 检测是否重复              
                if($cheCkgl){
                    $res = ['statusCode'=>300,'message'=>'仓库信息已存在'];
                }else{ 
                    if($update = CkglModel::update($data)){
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
                    CkglModel::destroy($value);
                }
                $res = ['statusCode'=>200,'message'=>'操作完成'];
            }else{
                $res = ['statusCode'=>300,'message'=>'操作失败'];
            }
            echo json_encode($res);
        }

        public function set(){
            $id = $this->request->param('id');
            $status = $this->request->param('status');
            if($id){
                CkglModel::update(['status'=>$status,'id'=>$id]);
                $res = ['statusCode'=>200,'message'=>'操作完成'];
            }else{
                $res = ['statusCode'=>300,'message'=>'操作失败'];
            }
            echo json_encode($res);
        }


        

        


    }
