<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/26
 * Time: 10:19
 */

namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Ctbp as CtbpModel;
use app\admin\model\Xmxx as XmxxModel;
use app\admin\model\Xmfl as XmflModel;
use app\admin\model\Jldw as JldwModel;


class Ctbp extends Base
{
    public function index(){
        // 获取数据
        $list = CtbpModel::where('title','like','%'.$this->request->param('keywords').'%')
            ->field('id,title as name,status')
            ->where('status',1)
            ->select();

        $this->assign('list',json_encode($list));
        //echo json_encode($xmfl->getAuths($list,0,0));
        return $this->fetch('qtzd/ctbp/index');
    }

    public function xmlist(){
        $cid = $this->request->param('flid');
        $list = [];
        $totalCounts = 0;
        $totalPrice = 0;
        if($cid){
            // 获取数据
            $ctbp = CtbpModel::all(['status'=>1,'id'=>$cid]);
        }else{
            $ctbp = false;
        }
        if($ctbp){
            foreach($ctbp as $k=>$v){
                $xmdatas = json_decode($v['datas'],true);
                if($xmdatas){
                    foreach($xmdatas as $xk=>$xv){
                        $xmlist = XmxxModel::where('id',$xv['xm_id'])->find();
                        $xmfl = XmflModel::where('id',$xmlist['flid'])->where('status',1)->find();
                        if($xmlist){
                            $xmlist['title'] = $xmfl['title'];
                            $xmlist['nums'] = $xv['nums'];
                            $xmlist['totalje'] = $xv['nums']*$xmlist['sj'];
                            $list[] = $xmlist;
                            $totalCounts += 1;
                            $totalPrice += $xmlist['totalje'];
                        }
                    }
                }
            }
        }

        $this->assign('list',$list);
        $this->assign('totalcounts',$totalCounts);
        $this->assign('totalprice',$totalPrice);
        $this->assign('cid',$cid);
        return $this->fetch('qtzd/ctbp/list');
    }

    public function add(){
        return $this->fetch('qtzd/ctbp/add');
    }

    public function adddo(){
        $data = array(
            'title' => $this->request->param('title'),
        );
        if(count($data)>=1){
            $check = CtbpModel::get(['title'=>$data['title']]);
            // 检测是否重复
            if($check){
                $res = ['statusCode'=>300,'message'=>'成套殡品已存在'];
            }else{
                if($insert = CtbpModel::create($data)){
                    $res = ['statusCode'=>200,'message'=>'操作成功'];
                }else{
                    $res = ['statusCode'=>300,'message'=>'操作失败'];
                }
            }
        }else{
            $res = ['statusCode'=>300,'message'=>'数据录入错误'];
        }

        echo json_encode($res);
    }

    public function delete(){
        $ids = explode(',',$this->request->param('ids'));
        if(count($ids)>0){
            $error = 0;
            foreach($ids as $key=>$value){
                $check = CtbpModel::get($value);
                if(empty($check->datas)){
                    CtbpModel::destroy($value);
                }else{
                    $error++;
                }
            }
            $res = ['statusCode'=>200,'message'=>'操作完成'];
        }else{
            $res = ['statusCode'=>300,'message'=>'操作失败'];
        }
        echo json_encode($res);
    }

    // 新增成套殡品
    public function addxm(){
        // 获取数据
        $list = XmxxModel::alias('a')
            ->field('a.*,b.title')
            ->join('xmfl b','a.flid=b.id')
            ->where('a.xmbh|a.xmmc|a.xmbm','like','%'.$this->request->param('keywords').'%')
            ->select();
        // 获取总数量
        $totalCounts = XmxxModel::where('xmbh|xmmc|xmbm','like','%'.$this->request->param('keywords').'%')
            ->count();
        $this->assign('list',$list);
        $this->assign('totalcounts',$totalCounts);
        $this->assign('cid',$this->request->param('cid'));
        return $this->fetch('qtzd/ctbp/xmlist');
    }

    public function addxmdo(){
        $ids = explode(',',$this->request->param('ids'));
        $cid = $this->request->param('cid');
        if(count($ids)>0 and $cid){
            
            // 定义存储成套殡品中所包含项目信息的空数组
            $xmDatas = array();
            // 读取成套殡品信息
            $ctbp = CtbpModel::get($cid);
            // 获取成套殡品中所包含的项目信息json格式，存在转数组
            if(!empty($ctbp['datas'])){
                $xmDatas = json_decode($ctbp['datas'],TRUE);
            }
            // 临时存储成套殡品中所包含新项目信息的空数组
            $newxmxxDatas = array();
            //$test = [];
            if(empty($xmDatas)==1){// 不存在任何项目信息
                foreach($ids as $key=>$value){              
                    // 更新数组的对应数据
                    $xmDatas[] = array(
                        'xm_id' => (int)$value,
                        'nums' => 1
                    );
                }
            }else{ // 存在项目信息
                
                foreach($ids as $key=>$value){ 
                    $i = 0;
                    foreach($xmDatas as $k=>$v){
                        if($v['xm_id'] == $value){
                            $i = $k;
                        }
                    }
                    if($i==0){
                        // 追加
                        $newxmxxDatas[] = ['xm_id'=>$value,'nums'=>1];
                    }else{
                        // 更新数组的对应数据
                        $xmDatas[$i]['nums'] += 1;
                    }
                    
                }
                
                if(empty($newxmxxDatas)!==1){
                    $xmDatas = array_merge($xmDatas,$newxmxxDatas);
                }
            }
            // 更新成套殡品中所包含的项目信息
            if($update = CtbpModel::update(['id'=>$cid,'datas'=>json_encode($xmDatas)])){
                $res = ['statusCode'=>200,'message'=>'操作成功'];
            }else{
                $res = ['statusCode'=>300,'message'=>'操作失败'];
            }
        }else{
            $res = ['statusCode'=>300,'message'=>'非法操作'];
        }
        echo json_encode($res);
    }

    public function editxm(){
        $xmfl = new XmflModel();
        $xmlist = XmflModel::all(['status'=>1]);
        // 定义存储成套殡品中所包含项目信息的空数组
        $xmDatas = array();
        $cid = $this->request->param('cid');
        $id = $this->request->param('id');
        // 读取成套殡品信息
        $ctbp = CtbpModel::get($cid);
        // 获取成套殡品中所包含的项目信息json格式，存在转数组
        $xmDatas = json_decode($ctbp['datas'],TRUE);
        // 获取数组包含的对应项目的xm_id和nums
        $i = 0;
        foreach($xmDatas as $key=>$value){
            if($value['xm_id']==$id){
                $i = $key;
            }
        }

        $this->assign('plist',json_encode($xmfl->getAuths($xmlist,0,0)));
        $this->assign('dwlist',JldwModel::all());
        $this->assign('info',XmxxModel::where('id',$id)->find());
        $this->assign('cid',$cid);
        $this->assign('nums',$xmDatas[$i]['nums']);

        return $this->fetch('qtzd/ctbp/editxm');
    }

    public function editxmdo(){
        $xmdata = array(
            'id' => $this->request->param('id'),
            'xmmc' => $this->request->param('xmmc'),
            'xmbm' => $this->request->param('xmbm'),
            'dw' => $this->request->param('dw'),
            'sj' => $this->request->param('sj'),
            'flid' => $this->request->param('flid'),
        );
        $cid = $this->request->param('cid');
        if(count($xmdata)>=6 and $cid){
            $check = XmxxModel::where('xmmc',$xmdata['xmmc'])->where('id','<>',$xmdata['id'])->find();
            // 检测是否重复
            if($check){
                $res = ['statusCode'=>300,'message'=>'项目已存在'];
            }else{
                if($update = XmxxModel::update($xmdata)){
                    $xmDatas = array();
                    // 读取成套殡品信息
                    $ctbp = CtbpModel::get($cid);
                    $xmDatas = json_decode($ctbp['datas'],TRUE);
                    foreach($xmDatas as $key=>$value){
                        if($value['xm_id']==$xmdata['id']){
                            $xmDatas[$key]['nums'] = $this->request->param('nums');
                        }
                    }
                    if($update = CtbpModel::update(['id'=>$cid,'datas'=>json_encode($xmDatas)])){
                        $res = ['statusCode'=>200,'message'=>'操作成功'];
                    }else{
                        $res = ['statusCode'=>300,'message'=>'操作失败'];
                    }
                }else{
                    $res = ['statusCode'=>300,'message'=>'操作失败'];
                }
            }
        }else{
            $res = ['statusCode'=>300,'message'=>'数据录入错误'];
        }

        echo json_encode($res);
    }

    public function deletexm(){
        $ids = explode(',',$this->request->param('ids'));
        $cid = $this->request->param('cid');
        if(count($ids)>0 and $cid){
            $xmDatas = array();
            // 读取成套殡品信息
            $ctbp = CtbpModel::get($cid);
            $xmDatas = json_decode($ctbp['datas'],TRUE);
            foreach($ids as $key=>$value){
                $i = 0;
                foreach($xmDatas as $k=>$v){
                    if($v['xm_id'] == $value){
                        $i = $k;
                    }
                }
                unset($xmDatas[$i]);
            }
            if($update = CtbpModel::update(['id'=>$cid,'datas'=>json_encode($xmDatas)])){
                $res = ['statusCode'=>200,'message'=>'操作成功'];
            }else{
                $res = ['statusCode'=>300,'message'=>'操作失败'];
            }
        }else{
            $res = ['statusCode'=>300,'message'=>'操作失败'];
        }
        echo json_encode($res);
    }


}