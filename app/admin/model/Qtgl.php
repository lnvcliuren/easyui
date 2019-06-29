<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/27
 * Time: 20:01
 */

namespace app\admin\model;
use think\Model;
use think\Db;

class Qtgl extends Model
{
    protected $name = 'register';
    protected $type       = [
        // 设置创建时间为时间戳类型（整型）
        'create_time' => 'timestamp:Y-m-d H:i:s',
    ];
    // 定义时间戳字段名
    protected $createTime = 'czrq';
    protected $updateTime = 'update_time';
    // 开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    // 定义自动完成的属性
    protected $insert = ['status' => 0];
    // 创建时间读取器
    public function getCzrqAttr($value){
        return date('Y-m-d H:i:s',$value);
    }
    // 创建时间修改器
    public function setCzrqAttr($value){
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
    // status获取器
    public function getStatusAttr($value){
        $status = [-1=>'删除',0=>'启用',1=>'归档'];
        return $status[$value];
    }
    // 是否存尸获取器
    public function getIsCsAttr($value){
        $status = [0=>'是',1=>'否'];
        return $status[$value];
    }

}