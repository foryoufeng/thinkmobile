<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/22
 * Time: 10:14
 */

namespace Home\Model;


use Common\Model\CommonModel;

class CollectModel extends CommonModel
{
    private  $success=array('code'=>0,'msg'=>'success');
    private  $fail=array('code'=>1,'msg'=>'fail');
    private  $nologin=array('code'=>2,'msg'=>'please login first');
    private  $nogoods=array('code'=>3,'msg'=>'no goods_id');
    private  $hgoods=array('code'=>4,'msg'=>'goods already exists');
    private  $norec=array('code'=>5,'msg'=>'no rec id');
    private $user_id;
    protected $tableName='Collect_goods';
    protected function _initialize() {
        $this->user_id=session('user.user_id');
    }
    public function getAll(){
//        $user_id=session(user.user_id);
//        if(!$user_id){
//            return $this->nologin;
//        }
        $page=I('p',1);
        $str1="近1个月";
        $str2="近2个月";
        //鑾峰彇褰撳墠鏃堕棿鎴?
        $time=strtotime('now');
        //鑾峰彇璺濈褰撳墠鏃堕棿1涓湀鍓嶆椂闂存埑
        $ftime=$time-(30 * 24 * 60 * 60);
        //鑾峰彇璺濈褰撳墠鏃堕棿2涓湀鍓嶆椂闂存埑
        $etime=$time-(60 * 24 * 60 * 60);
        //鑾峰彇杩?1涓湀鐢ㄦ埛鏀惰棌鐨勫晢鍝?
        $where['user_id']=$this->user_id;
        $where['add_time']=array(array('egt',$ftime),array('elt',$time));
        $oneMonth=$this->where($where)->select();
        //鑾峰彇1涓湀鍐呯敤鎴锋敹钘忓晢鍝佷俊鎭?
        $res1=array();
        $list[0]['name']=$str1;
        for($i=0;$i<count($oneMonth);$i++){
            $where3['goods_id']=$oneMonth[$i]['goods_id'];
            $res1[$i]=D('Goods')->field('goods_id,goods_name,shop_price,goods_thumb')->where($where3)->select();
            $list[0]['goods'][$i]=$res1[$i][0];
        }
        //print_r($res1);
        //鑾峰彇杩?2涓湀鐢ㄦ埛鏀惰棌鐨勫晢鍝?
        $where2['add_time']=array(array('egt',$etime),array('lt',$ftime));
        $twoMonth=$this->where($where)->where($where2)->select();
        //鑾峰彇2涓湀鍐呯敤鎴锋敹钘忓晢鍝佷俊鎭?
        $res2=array();
        $list[1]['name']=$str2;
        for($j=0;$j<count($twoMonth);$j++){
            $where4['goods_id']=$twoMonth[$j]['goods_id'];
            $res2[$j]=D('Goods')->field('goods_id,goods_name,shop_price,goods_thumb,is_on_sale')->where($where4)->select();
            $list[1]['goods'][$j]=$res2[$j][0];
        }

        return $list;
    }
    //鐢ㄦ埛娣诲姞鏀惰棌鍟嗗搧
    public function addData(){
        $user_id=session('user.user_id');
        if(!$user_id){
            return $this->nologin;
        }else{
            $goods_id=I('get.goods_id');//蹇呬紶鍙傛暟锛岃幏寰楁敹钘忕殑閭ｄ釜鍟嗗搧
            if($goods_id==''){
                return $this->nogoods;
            }
            $where['goods_id']=$goods_id;
            $where['user_id']=$user_id;
            $res=$this->where($where)->find();
            if($res){
                return $this->hgoods;
            }
            $data['goods_id']=$goods_id;
            $data['user_id']=$user_id;
            $data['add_time']=time();
            $list =parent::addData($data);
            if($list==CommonModel::MSUCCESS){
                return $this->success;
            }else{
                return $this->fail;
            }
        }

    }

    //鐢ㄦ埛鍒犻櫎鍟嗗搧鏀惰棌
    public function del(){
        $goods_id=I('get.goods_id');//蹇呬紶鍙傛暟锛岃幏寰楄鍒犻櫎鐨勬敹钘忓晢鍝?
        $user_id=session('user.user_id');
        $where['goods_id']=$goods_id;
        $where['user_id']=$user_id;
        $list=$this->where($where)->delete();
        if($list==CommonModel::MFAIL){
            return $this->success;
        }else{
            return $this->fail;
        }
    }

    //我的购物车编辑页面移至收藏
    public function move(){
        $goods_id=I('goods_id',0);//必传参数，获取移入收藏的商品id
        $rec_id=I('rec_id',0);
        $user_id=session('user.user_id');
        if(!$goods_id ||!$rec_id){//判断是否传递goods_id
            return $this->fail;
        }
        $where['goods_id']=$goods_id;
        $where['user_id']=$user_id;
        $res=$this->where($where)->find();
        if($res){//已经收藏该商品
            $where1=array(
                'user_id'=>$this->user_id,
                'rec_id'=>I('rec_id'),
            );
            $res2=D('Cart')->where($where1)->delete();
            if($res2){
                return $this->success;
            }else{
                return $this->fail;
            }
        }
        //将商品移至收藏
        $data['goods_id']=$goods_id;
        $data['user_id']=$user_id;
        $data['add_time']=time();
        //$res1 =parent::addData($data);
        $res1=$this->add($data);
        //在我的购物车中将该商品删除
        $where1=array(
            'user_id'=>$this->user_id,
            'rec_id'=>I('rec_id'),
        );
        $res2=D('Cart')->where($where1)->delete();
        if($res1 && $res2){
            return $this->success;
        }else{
            return $this->fail;
        }

    }





}