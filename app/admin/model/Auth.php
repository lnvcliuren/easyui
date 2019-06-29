<?php

    namespace app\admin\model;
    use think\Model;
    use think\Db;

    class Auth extends Model
    {
        protected  $name = 'auth';

        // is_menu读取器
        public function getIsMenuAttr($value){
            $is_menu = [0=>'否',1=>'是'];
            return $is_menu[$value];
        }

        // 绑定角色，多对多
        public function roles(){
            return $this->belongsToMany('Role','role_auth','role_id','auth_id');
        }

        // 查询所有菜单项
        public function getMenus(){
            $cateres =[];
            // 获取顶级菜单
            $topMeuns = Db::name('menu')->where('status',1)->order('sort','asc')->select();
            if($topMeuns){
                foreach($topMeuns as $k=>$v){
                    //获取导航列表及子列表
                    $cateres[$k] = Db::name('auth')
                            ->where('pid',0)
                            ->where('is_menu',1)
                            ->where('menu_id',$v['id'])
                            ->select();
                    foreach($cateres[$k] as $ck=>$cv){
                        $children = Db::name('auth')
                                ->where('pid',$cv['id'])
                                ->where('is_menu',1)
                                ->where('menu_id',$v['id'])
                                ->select();
                        if($children){
                            $cateres[$k][$ck]['children'] = $children;
                        }else{
                            $cateres[$k][$ck]['children'] = 0;
                        }
                    }
                }
                return $cateres;
            } else {
                return false;
            }             
        }

        // 查询所有的规则项
        public function getAuths($keywords){
            $cateres =[];
            // 获取顶级菜单
            $topAuths = $this::where('title|auth','like','%'.$keywords.'%')->where('pid',0)->select();
            if($topAuths){
                $cateres = $topAuths;
                foreach($topAuths as $k=>$v){
                    //获取导航列表及子列表
                    $cateres[$k] = $this::where('pid',$v['id'])->select();
                    if($cateres[$k]){
                        foreach($cateres[$k] as $ck=>$cv){
                            $children = $this::where('pid',$cv['id'])->select();
                            if($children){
                                $cateres[$k][$ck]['children'] = $children;
                            }else{
                                $cateres[$k][$ck]['children'] = false;
                            }
                        }
                    }
                }
                foreach($topAuths as $k=>$v){
                    $topAuths[$k]['children'] = $cateres[$k];
                }
                return $topAuths;
            } else {
                return false;
            }       
        }

    }