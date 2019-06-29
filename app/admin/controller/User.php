<?php

    namespace app\admin\controller;
    use app\admin\controller\Base;
    use app\admin\model\User as UserModel;
    use app\admin\model\Role as RoleModel;
    use think\Config;

    class User extends Base
    {
        public function index(){

            $datemin = $this->request->param('datemin')?strtotime($this->request->param('datemin').' 24:00:00'):'';
            $datemax = $this->request->param('datemax')?strtotime($this->request->param('datemax').' 24:00:00'):time();
            // 获取总数
            $totalCounts = UserModel::where('uname|email|phone','like','%'.$this->request->param('keywords').'%')
                            ->where('create_time','between',[$datemin,$datemax])
                            ->count();

            // 获取数据
            $list = UserModel::where('uname|email|phone','like','%'.$this->request->param('keywords').'%')
                            ->where('create_time','between',[$datemin,$datemax])
                            ->select();   
            
            $this->assign('list',$list);
            $this->assign('totalcounts',$totalCounts);

            return $this->fetch();
        }

        public function add(){
            // 获取所有顶级角色
            $plist = RoleModel::where('pid',0)->select();
            // 获取所有二级角色
            foreach($plist as $k=>$v){
                $list = RoleModel::where('pid',$v['id'])->select();
                $plist[$k]['childrens'] = $list;
            }
            // 向模板传递信息
            $this->assign('plist',$plist);
            return $this->fetch();
        }

        public function adddo(){
            $view_replace_str = Config::get('view_replace_str');
            $salt = $view_replace_str['__SALT__'];
            $data = array(
                'uname' => $this->request->param('uname'),
                'upass' => $this->request->param('upass')?(md5($this->request->param('upass').$salt)):'',
                'name' => $this->request->param('name'),
                'sex' => $this->request->param('sex'),
                'email' => $this->request->param('email'),
                'phone' => $this->request->param('phone'),
                'status' => 1,
            );
            if(count($data)>=4){
                $check = UserModel::get(['uname'=>$data['uname']]);
                if($check){
                    $res = ['statusCode'=>300,'message'=>'用户已存在'];
                }else{
                    // 存储角色用户信息
                    $pid = $this->request->param('pid');
                    if($insert = UserModel::create($data)){
                        $user = UserModel::get($insert->id);
                        if($pid){
                                $roles = RoleModel::get($pid);
                                $user->roles()->attach($roles);
                        }
                        $res = ['statusCode'=>200,'message'=>'操作成功','navTabId'=>'user','callbackType'=>'closeCurrent'];
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
            // 获取所有顶级角色
            $plist = RoleModel::where('pid',0)->select();
            // 获取所有二级角色
            foreach($plist as $k=>$v){
                $list = RoleModel::where('pid',$v['id'])->select();
                $plist[$k]['childrens'] = $list;
            }
            // 获取当前用户信息
            $user = UserModel::get($this->request->param('id'));
            // 获取当前用户的角色信息
            $roles = $user->roles;
            // 向模板传递信息
            $this->assign('plist',$plist);
            $this->assign('info',$user);
            $this->assign('roles',$roles);
            return $this->fetch();          
        }

        public function editdo(){
            $view_replace_str = Config::get('view_replace_str');
            $salt = $view_replace_str['__SALT__'];
            $data = array(
                'id' => $this->request->param('id'),
                'uname' => $this->request->param('uname'),
                //'upass' => $this->request->param('upass')?(md5($this->request->param('upass').$salt)):'',
                'name' => $this->request->param('name'),
                'sex' => $this->request->param('sex'),
                'email' => $this->request->param('email'),
                'phone' => $this->request->param('phone'),
                'status' => 1,
            );
            if(count($data)>=3){
                $upass = $this->request->param('upass');
                if($upass){
                    $data['upass'] = $upass?(md5($upass.$salt)):'';
                }
                $check = UserModel::where('uname',$data['uname'])->where('id','<>',$data['id'])->select();
                if($check){
                    $res = ['statusCode'=>300,'message'=>'用户已存在'];
                }else{
                    // 存储角色用户信息
                    $pid = $this->request->param('pid');
                    if($update = UserModel::update($data)){
                        $user = UserModel::get($update->id);
                        $user->roles()->detach();
                        if($pid){                           
                            $role = RoleModel::get($pid);                              
                            $user->roles()->attach($role);
                        }
                        $res = ['statusCode'=>200,'message'=>'操作成功','navTabId'=>'user','callbackType'=>'closeCurrent'];
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
            $i = 0;
            if(count($ids)>0){
                foreach($ids as $key=>$value){
                    $user = UserModel::get($value);
                    if($user['status']=='禁用' and $value!=='1'){
                        UserModel::destroy($value);
                        $i++;
                    }                      
                }
                if($i>0){
                    $res = ['statusCode'=>200,'message'=>'操作完成','forwardUrl'=>'admin/user/index?navTabId=user','callbackType'=>'forward'];
                }else{
                    $res = ['statusCode'=>300,'message'=>'请先禁用操作项后在执行此操作'];
                }              
            }else{
                $res = ['statusCode'=>300,'message'=>'操作失败'];
            }
            echo json_encode($res);
        }

        public function set(){
            $id = $this->request->param('id');
            $f = $this->request->param('f');
            if($id and $f){
                $status = $f=='stop'?0:1;
                UserModel::update(['id'=>$id,'status'=>$status]);
                $res = ['statusCode'=>200,'message'=>'操作完成'];            
            }else{
                $res = ['statusCode'=>300,'message'=>'操作失败'];
            }
            echo json_encode($res);
        }


    }