<?php
namespace app\index\controller;
use think\Controller;
use app\admin\model\Auth as AuthModel;

class Index extends Controller
{
    public function index()
    {

    }

    public function getmenuitems(){
        $mid = $this->request->param('mid');
        $plist = AuthModel::where('pid',0)->where('menu_id',$mid)->select();
        foreach($plist as $k=>$v){
            $plist[$k]['children'] = AuthModel::where('pid',$v['id'])->select();
        }
        return json_encode($plist);
    }

}
