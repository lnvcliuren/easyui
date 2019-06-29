<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/25
 * Time: 19:35
 */

namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Xmfl as XmflModel;
use app\admin\model\Xmxx as XmxxModel;
use app\admin\model\Jldw as JldwModel;


class Xmxx extends Base
{
    public function index(){
        // 获取数据
        $list = XmflModel::where('title','like','%'.$this->request->param('keywords').'%')
            ->field('id,title as name,pid,status')
            ->select();

        $this->assign('list',json_encode($list));
        //echo json_encode($xmfl->getAuths($list,0,0));
        return $this->fetch('jcsz/Xmxx/index');
    }

    public function xmlist(){
        $flid = $this->request->param('flid');

        if($flid){
            // 获取数据
            $list = XmxxModel::alias('a')
                ->field('a.*,b.title')
                ->join('xmfl b','a.flid=b.id')
                ->where('a.xmbh|a.xmmc|a.xmbm','like','%'.$this->request->param('keywords').'%')
                ->where('flid',$this->request->param('flid'))
                ->select();
            // 获取总数量
            $totalCounts = XmxxModel::where('xmbh|xmmc|xmbm','like','%'.$this->request->param('keywords').'%')
                ->where('flid',$this->request->param('flid'))
                ->count();
        }else{
            // 获取数据
            $list = XmxxModel::alias('a')
                ->field('a.*,b.title')
                ->join('xmfl b','a.flid=b.id')
                ->where('a.xmbh|a.xmmc|a.xmbm','like','%'.$this->request->param('keywords').'%')
                ->select();
            // 获取总数量
            $totalCounts = XmxxModel::where('xmbh|xmmc|xmbm','like','%'.$this->request->param('keywords').'%')
                ->count();
        }

        $this->assign('list',$list);
        $this->assign('totalcounts',$totalCounts);
        return $this->fetch('jcsz/Xmxx/list');
    }

    public function add(){
        $xmfl = new XmflModel();
        $xmlist = XmflModel::all(['status'=>1]);
        $this->assign('plist',json_encode($xmfl->getAuths($xmlist,0,0)));
        $this->assign('dwlist',JldwModel::all());
        return $this->fetch('jcsz/Xmxx/add');
    }

    public function adddo(){
        $data = array(
            'xmmc' => $this->request->param('xmmc'),
            'xmbm' => $this->request->param('xmbm'),
            'dw' => $this->request->param('dw'),
            'sj' => $this->request->param('sj'),
            'flid' => $this->request->param('flid'),
        );
        if(count($data)>=5){
            $check = XmxxModel::where('xmmc',$data['xmmc'])->find();
            // 检测是否重复
            if($check){
                $res = ['statusCode'=>300,'message'=>'项目已存在'];
            }else{
                if($insert = XmxxModel::create($data)){
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

    public function edit(){
        $xmfl = new XmflModel();
        $xmlist = XmflModel::all(['status'=>1]);
        $this->assign('plist',json_encode($xmfl->getAuths($xmlist,0,0)));
        $this->assign('dwlist',JldwModel::all());
        $this->assign('info',XmxxModel::where('id',$this->request->param('id'))->find());
        return $this->fetch('jcsz/Xmxx/edit');
    }

    public function editdo(){
        $data = array(
            'id' => $this->request->param('id'),
            'xmmc' => $this->request->param('xmmc'),
            'xmbm' => $this->request->param('xmbm'),
            'dw' => $this->request->param('dw'),
            'sj' => $this->request->param('sj'),
            'flid' => $this->request->param('flid'),
        );
        if(count($data)>=6){
            $check = XmxxModel::where('xmmc',$data['xmmc'])->where('id','<>',$data['id'])->find();
            // 检测是否重复
            if($check){
                $res = ['statusCode'=>300,'message'=>'项目已存在'];
            }else{
                if($insert = XmxxModel::update($data)){
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
            foreach($ids as $key=>$value){
                XmxxModel::destroy($value);
            }
            $res = ['statusCode'=>200,'message'=>'操作完成'];
        }else{
            $res = ['statusCode'=>300,'message'=>'操作失败'];
        }
        echo json_encode($res);
    }




}