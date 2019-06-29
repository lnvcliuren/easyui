<?php

namespace app\admin\model;
use think\Model;

class Ckgl extends Model
{
    protected $name  = 'ck';

    // 定义自动完成的属性
    protected $insert = ['status' => 1];

    // 创建时间读取器
    public function getCreateTimeAttr($value){
        return date('Y-m-d H:i',$value);
    }

    // status获取器
    public function getStatusAttr($value){
        $status = [0=>'禁用',1=>'启用'];
        return $status[$value];
    }

    // 查询所有的规则项
    public function getAuths($keywords){
        $cateres =[];
        // 获取顶级菜单
        $topAuths = $this::where('title|no|lxr|phone','like','%'.$keywords.'%')->where('pid',0)->select();
        if($topAuths){
            $cateres = $topAuths;
            foreach($topAuths as $k=>$v){
                if($v){
                    //获取导航列表及子列表
                    $cateres[$k]['childrens'] = $this::where('pid',$v['id'])->select();
                }
            }
            return $cateres;
        } else {
            return false;
        }
    }

}