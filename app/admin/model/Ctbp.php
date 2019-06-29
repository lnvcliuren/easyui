<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/26
 * Time: 10:13
 */

namespace app\admin\model;
use think\Model;

class Ctbp extends Model
{
    protected  $name = 'ctbp';

    protected $type       = [
        // 设置创建时间为时间戳类型（整型）
        'create_time' => 'timestamp:Y-m-d H:i:s',
    ];

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;

    // 定义自动完成的属性
    protected $insert = ['status' => 1];

    // 创建时间读取器
    public function getCreateTimeAttr($value){
        return date('Y-m-d H:i',$value);
    }

    // 创建时间修改器
    public function setCreateTimeAttr($value){
        return strtotime($value);
    }

    // 更新时间读取器
    public function getUpdateTimeAttr($value){
        return date('Y-m-d H:i',$value);
    }

    // 更新时间修改器
    public function setUpdateTimeAttr($value){
        return strtotime($value);
    }

    public function getStatusAttr($value){
        $status = [0=>'禁用',1=>'启用'];
        return $status[$value];
    }

}