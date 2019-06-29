<?php

    namespace app\admin\controller;
    use think\Controller;
    use think\Request;
    use app\admin\model\User;
    use think\Config;
    use think\Session;
    use think\App;
    
    class Login extends Controller
    {
        public function index(){
           return $this->fetch();
        }

        public function dialog(){
            return $this->fetch('login/dialog');
        }

        public function check(Request $request){
            $uname = $request->param('uname');
            $upass = $request->param('upass');
            if($uname and $upass){
                $user = User::get(['uname'=>$uname,'upass'=>sha1(md5($upass.Config::get('app_salt')))]);
                if($user){  
                    User::where('uname',$uname)->update(['last_time'=>time(),'login_counts'=>$user->login_counts+1]);                 
                    $roles = $user->roles()->select();
                    Session::set('efing.uname',$uname);
                    Session::set('efing.roles',$roles);
                    $this->success('欢迎使用'.Config::get('app_name').'!',url('/'));
                }else{
                    $this->error('用户名或密码错误',url('/login'));
                }
            } else {
                $this->error('请正确输入用户名和密码',url('/login'));
            }
        }
    }