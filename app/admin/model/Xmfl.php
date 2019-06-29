<?php

namespace app\admin\model;
use think\Model;

class Xmfl extends Model
{
    protected $name  = 'xmfl';

    // 查询所有的分类项
    public function getAuths($arr,$id,$level){
        $list =array();
        foreach ($arr as $k=>$v){
            if ($v['pid'] == $id){
                $v['level']=$level;
                $v['son'] = $this->getAuths($arr,$v['id'],$level+1);
                $list[] = $v;
            }
        }
        return $list;
    }
}