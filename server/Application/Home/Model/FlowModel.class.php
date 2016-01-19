<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/22
 * Time: 10:14
 */

namespace Home\Model;


use Common\Model\CommonModel;

class FlowModel extends CommonModel
{
    const WXPAY='wxpay';
    const ALIPAY='alipay';
    protected $tableName='Cart';
    private  $goods;//商品模型
    private  $address;//商品模型
    private  $user_id;//用户id
    private  $success=array('code'=>0,'msg'=>'success');//成功
    private  $fail=array('code'=>4,'msg'=>'fail');//成功
    private  $noaddr=array('code'=>1,'msg'=>'no address');//没有收获地址
    private  $nogoods=array('code'=>2,'msg'=>'no goods');//没有商品
    public function _initialize(){
        $this->user_id=session('user.user_id');
        $this->goods=M('goods');
        $this->address=D('Address');
    }
    public function getAll(){
        $list=null;
        $addr=$this->address->getOne();
        if(!$addr){
            return $this->noaddr;//没有收获地址
        }
        $list['address']=$addr;
        $list['brand']=$this->getGoods();//获取商品信息
        $list['express']=$this->getExpress();//获取物流信息
        return $list;
    }

    /**
     * 获取商品信息
     * @return array|void
     */
    private function getGoods(){
        $rec_id=I('rec_id','0');
        $result=array();
        $where=array(
            'rec_id'=>array('in',$rec_id),
            'user_id'=>$this->user_id
        );
        $data=$this->alias('c')->join('LEFT JOIN __GOODS__ g on g.goods_id = c.goods_id')
            ->field('c.goods_id,c.goods_number,c.goods_price,c.goods_name,c.goods_attr,c.rec_id,c.product_id,g.goods_thumb')
            ->where($where)->select();
        if(!$data){
            return $this->msg($this->nogoods);
        }
        foreach($data as $k=>$v){
            $id=$v['goods_id'];
            $res=$this->goods->alias('g')->join('LEFT JOIN __BRAND__ b on g.brand_id = b.brand_id')
                ->field('g.*,b.*')->where(array('goods_id'=>$id))->select();
            $brand_id=$res[0]['brand_id'];
            $list['brand'][$brand_id]['brand_logo']=$res[0]['brand_logo'];
            $list['brand'][$brand_id]['brand_name']=$res[0]['brand_name'];
            $list['brand'][$brand_id]['goods'][]=$v;
        }
        foreach($list['brand']  as $k=>$v){
            $result[]=$v;
        }
        foreach($result as $k=>$v){
            $result[$k]['count']=0;
            $result[$k]['price']=0.00;
            foreach($v['goods'] as $key=>$val){
                $result[$k]['count']+=$val['goods_number'];
                $result[$k]['price']+=$val['goods_price']*$val['goods_number'];
            }
        }
        return $result;
    }

    /**
     * 获取物流信息
     * @return null
     */
    private function getExpress(){
        $data=null;
        $res=M('ShippingArea')->alias('sa')->join('LEFT JOIN __SHIPPING__ s on sa.shipping_id = s.shipping_id')
            ->field('sa.*,s.*')->select();
        if($res){
            foreach($res as $k=>$v) {
                $data[$k]['shipping_name'] = $res[$k]['shipping_name'];
                $data[$k]['shipping_id'] = $res[$k]['shipping_id'];
                $config = unserialize($res[$k]['configure']);
                foreach ($config as $val) {
                    if ($val['name'] == 'base_fee') {
                        $data[$k]['base_fee'] = $val['value'];
                        break;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 生成订单
     */
    public function generate(){
        $info=array();
        $address_id=I('address_id',0);//地址id
        $shipping_id=I('shipping_id',0);
        $pay=I('pay',"no");
        $addr=$this->getAddr($address_id);//地址信息
        if(!$addr){
            return $this->noaddr;//没有收获地址
        }
        $express=$this->getShip($shipping_id);
        $order_sn=$this->order_sn();//生成订单号
        $goods=$this->getPrice();//获取所有商品的
        $info=array_merge($addr,$express,$order_sn,$goods);
        $info['user_id']=$this->user_id;
        $info['email']=session('user.email');
        $info['surplus']=round($goods['goods_amount']+$express['shipping_fee'],2);
        if($pay==FlowModel::ALIPAY){
            $info['pay_id']=4;
            $info['pay_name']=FlowModel::ALIPAY;
        }elseif($pay==FlowModel::WXPAY){
            $info['pay_id']=2;
            $info['pay_name']=FlowModel::WXPAY;
        }
        $id=D('Order')->adds($info);//获取订单的id
        if($id){
            $data=$this->getCarts();
            $this->where(array('user_id'=>$this->user_id,
                'rec_id'=>array('in',I('rec_id',0)),
            ))->delete();//删除购物车中的商品
            foreach($data as $v){
                $v['order_id']=$id;
                unset($v['rec_id']);
                M('OrderGoods')->data($v)->add();
            }
            $msuccess['id']=$id;
            $msuccess['msg']='success';
            return $msuccess;
        }else{
            return $this->fail;
        }
        //return $info;
    }

    /**
     * //获取购物车中的商品
     * @return mixed
     */
    private function getCarts(){
        $where=array(
            'user_id'=>$this->user_id,
            'rec_id'=>array('in',I('rec_id',0)),
        );
        $result=$this->where($where)->select();
        return  $result;
    }
    private function getPrice(){
        $result=$this->getCarts();//获取购物车中的商品
        if($result){
            $amount=0;
            foreach($result as $v){
                $money=$v['goods_price']*$v['goods_number'];
                $amount+=$money;
            }
            $data['goods_amount']=round($amount,2);
        }else{
            $this->msg($this->nogoods);
        }
        return $data;
    }
    /**
     * 获取地址信息
     * @param $address_id
     * @return mixed
     */
    private function getAddr($address_id){
        $where=array(
            'user_id'=>$this->user_id,
            'address_id'=>$address_id,
        );
        $addr=$this->address->field('province,city,district,consignee,address,mobile')->where($where)->find();
        return $addr;
    }
    private function getShip($shipping_id){
        $data=array();
        $where=array(
            's.shipping_id'=>$shipping_id,
        );
        $res=M('ShippingArea')->alias('sa')->join('LEFT JOIN __SHIPPING__ s on sa.shipping_id = s.shipping_id')
            ->field('sa.*,s.*')->where($where)->find();
        if($res){
            $data['shipping_name'] = $res['shipping_name'];
            $data['shipping_id'] = $res['shipping_id'];
                $config = unserialize($res['configure']);
                foreach ($config as $val) {
                    if ($val['name'] == 'base_fee') {
                        $data['shipping_fee'] = $val['value'];
                        break;
                    }
                }
        }
        return $data;
    }
    private function order_sn(){
        $data=array();
        $str=date('Ymdhis',time());
        $str.=rand(1000,9999);
        $data['order_sn']=$str;
        return $data;
    }
}