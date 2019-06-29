<?php

    namespace app\admin\model;
    use think\Model;
    use think\Session;

    class Role extends Model
    {
        protected $name = 'role';

        // status获取器
        public function getStatusAttr($value){
            $status = [-1=>'删除',0=>'禁用',1=>'启用'];
            return $status[$value];
        }

        // 检测权限
        public function checkAuth($module,$controller,$action){
            $loginroles = Session::get('efing.roles');
            $role = $this::get($loginroles[0]['id']);
            $auths = $role->auths()->select();
            $is_auth = false;
            $currentAuth = $module.'/'.$controller.'/'.$action;   
            foreach($auths as $k=>$v){
                if($v['auth']!=NULL and $currentAuth!=NULL){
                    if(strtolower($v['auth'])==strtolower($currentAuth) or strtolower($v['auth']).'do'==strtolower($currentAuth)){
                        $is_auth = true;
                    }
                }             
            }
            return $is_auth;
        }

        // 返回登录角色信息
        public function getInfo(){
            $loginroles = Session::get('efing.roles');
            return $loginroles[0]['name'];
        }

        // 绑定角色关联，多对多
        public function users(){
            return $this->belongsToMany('User','user_role','user_id','role_id');
        }

        // 绑定规则关联，多对多
        public function auths(){
            return $this->belongsToMany('Auth','role_auth','auth_id','role_id');
        }
    }