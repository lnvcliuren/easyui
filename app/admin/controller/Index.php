<?php

    namespace app\admin\controller;
    use app\admin\controller\Base;
    use think\Session;
    
    class Index extends Base
    {
        public function admin(){
            return $this->fetch('index/welcome');
        }

        public function index(){
            return $this->fetch();
        }

        public function welcome(){
            return $this->fetch('index/welcome');
        }

        public function logout(){
            if(Session::has('efing.uname') or Session::has('efing.roles')){
                Session::delete('efing.uname');
                Session::delete('efing.roles');
                session_destroy();
            }
            $this->success('感谢的所作的工作和付出！',url('\login'));
        }
    }