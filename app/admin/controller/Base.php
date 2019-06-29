<?php

    namespace app\admin\controller;
    use think\Controller;
    use think\Session;
    use think\Request;
    use app\admin\model\Role;
    use app\admin\model\Auth;
    use app\admin\model\User;
    use app\admin\model\Menu;

    class Base extends Controller
    {
        public function _initialize(){
            // 检测登录状态
            if(!Session::has('efing.uname') or !Session::has('efing.roles')){
                return $this->redirect('\login');
            }

            // 当前访问规则
            $m = $this->request->module();
            $c = $this->request->controller();
            $a = $this->request->action();

            // 检测权限
            $role = new Role();
            if($role->checkAuth($m,$c,$a)==false){
                //echo $m.'/'.$c.'/'.$a;
                echo "<script>alert('当权用户无权操作此项');window.top.location='".url('admin/index/index')."';</script>";
                exit();
            }

            // 获取菜单
            $auth = new Auth();
            //dump($auth->getMenus());
            $this->assign('menus',$auth->getMenus());

            // 获取顶级菜单
            $this->assign('topmenus',Menu::where('status',1)->order('sort','asc')->select());

            // 当前访问规则
            $this->assign('url',$m.'/'. $c .'/'.$a );

            // 当前登录用户信息
            $this->assign('current_user',User::get(['uname'=>$this->request->session('efing.uname')]));
            $this->assign('current_role',$role->getInfo());
        }
    }