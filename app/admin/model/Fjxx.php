<?php

namespace app\admin\model;
use think\Model;

class Fjxx extends Model
{
    protected $name  = 'fjxx';

    // 定义自动完成的属性
    protected $insert = ['status' => 1];

    // 定义关联方法
    public function fjlb()
    {
        // 用户HAS ONE档案关联
        return $this->hasOne('Fjlb','fjlb_id','id');
    }

}