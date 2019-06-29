<?php

    namespace app\admin\controller; 
    use app\admin\controller\Base;
    use app\admin\model\Fjxx as FjxxModel;
    use app\admin\model\Fjlb;

    class Fjxx extends Base
    {
        public function index(){
            // 获取数据
            $list = FjxxModel::alias('a')
                            ->join('fjlb b','a.fjlb_id = b.id')
                            ->field('a.*,b.title')
                            ->where('b.title','like','%'.$this->request->param('keywords').'%')  
                            ->select();           
            $this->assign('list',$list);
            $this->assign('totalcounts',FjxxModel::count());
            
            return $this->fetch('base/Fjxx/index');
        }

        public function add(){
            $this->assign('list',Fjlb::all());
            return $this->fetch('base/Fjxx/add');
        }

        public function adddo(){
            $data = array(
                'fjlb_id' => $this->request->param('fjlb_id'),
                'price' => $this->request->param('price'),
            );
            if(count($data)>=2){
                if($insert = FjxxModel::create($data)){
                    $res = ['statusCode'=>200,'message'=>'操作成功'];
                }else{
                    $res = ['statusCode'=>300,'message'=>'操作失败'];
                }                                       
            }else{
                $res = ['statusCode'=>300,'message'=>'数据录入错误'];
            }
           
            echo json_encode($res);
        }

        public function edit(){
            $this->assign('info',FjxxModel::get($this->request->param('id')));
            $this->assign('list',Fjlb::all());
            return $this->fetch('base/Fjxx/edit');
        }

        public function editdo(){
            $data = array(
                'id' => $this->request->param('id'),
                'fjlb_id' => $this->request->param('fjlb_id'),
                'price' => $this->request->param('price'),
            );
            if(count($data)>=3){
                if($update = FjxxModel::update($data)){
                    $res = ['statusCode'=>200,'message'=>'操作成功'];
                }else{
                    $res = ['statusCode'=>300,'message'=>'操作失败'];
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
                    FjxxModel::destroy($value);
                }
                $res = ['statusCode'=>200,'message'=>'操作完成'];
            }else{
                $res = ['statusCode'=>300,'message'=>'操作失败'];
            }
            echo json_encode($res);
        }

        public function input(){
            return $this->fetch('base/fjxx/input');
        }

        public function inputdo(){
            $file = $_FILES['file_0'];
            $ext = explode('.',$_FILES['file_0']['name']);
            //$data = $this->import_excel($file);
            if($ext[1]=='xls' or $ext[1]=='xlsx'){
                // 获取数据
                $data = $this->import_excel($file['tmp_name']);
                // 将数据导入数据表
                foreach($data as $k=>$v){
                    $insert_data[$k] = ['fjlb_id'=>$v[2],'price'=>$v[1]];            
                }   
                FjxxModel::insertAll($insert_data);
                echo '<script>
                    alert("操作完成");
                    var index = parent.layer.getFrameIndex(window.name);
                    window.parent.location.reload();
                </script>';
                       
            }else{
                echo '请选择符合要求的Excel文件上传';
                echo '<script>
                        alert("请选择符合要求的Excel文件上传");
                    </script>';
            }

        }

        private function import_excel($file){
            // 判断文件是什么格式
            $type = pathinfo($file);
            $type = strtolower($type["extension"]);
            $type=$type==='csv' ? $type : 'Excel5';
            ini_set('max_execution_time', '0');
            Vendor('PHPExcel1_8.PHPExcel');
            // 判断使用哪种格式
            $objReader = \PHPExcel_IOFactory::createReader($type);
            $objPHPExcel = $objReader->load($file);
            $sheet = $objPHPExcel->getSheet(0);
            // 取得总行数
            $highestRow = $sheet->getHighestRow();
            // 取得总列数
            $highestColumn = $sheet->getHighestColumn();
            //循环读取excel文件,读取一条,插入一条
            $data=array();
            //从第一行开始读取数据
            for($j=2;$j<=$highestRow;$j++){
                //从A列读取数据
                for($k='A';$k<=$highestColumn;$k++){
                    // 读取单元格
                    $data[$j][]=$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
                }
            }
            return $data;
        }


    }
