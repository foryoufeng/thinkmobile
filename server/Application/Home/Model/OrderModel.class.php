<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/16
 * Time: 11:48
 */

namespace Home\Model;


use Common\Model\CommonModel;

class OrderModel extends CommonModel
{
    private $user;
    protected $tableName='Order_info';
    private $order_goods;
    protected function _initialize() {
        $this->user=session('user');
        $this->order_goods=M('OrderGoods');
    }
    const NOPAY=1;//未付款
    const WSEND=2;//待发货
    const CANCEL=4;//已取消
    const WGET=3;//待收货
    const WCOMMENT=5;//待评价
    const BACK=6;//退货
    protected $_validate = array(
        array('user_id','require','用户id必须！'), //默认情况下用正则进行验证
        array('order_sn','require','订单号必须！'), // 在新增的时候验证name字段是否唯一
        array('pay_id',array(2,4),'pay_id必须！',1,'in'), // 在新增的时候验证name字段是否唯一
        array('pay_name','require','价格必须！'), // 在新增的时候验证name字段是否唯一
        array('surplus','require','实际价格必须！'), // 在新增的时候验证name字段是否唯一
        array('email','require','姓名必须！'), // 在新增的时候验证name字段是否唯一
        array('goods_amount','require','手机号必须！'), // 在新增的时候验证name字段是否唯一
        array('address','require','地址必须！'), // 在新增的时候验证name字段是否唯一
    );
    protected $_auto = array (
        array('add_time','time',1,'function'), // 对time字段在新增和更新的时候写入当前时间戳
        array('confirm_time','getTime',1,'callback'), // 对time字段在新增和更新的时候写入当前时间戳
        array('order_status',0), //
        array('shipping_status',0), //
        array('pay_status',0), //
        array('pay_fee',0), //
        array('pack_fee',0), //
        array('money_paid',0), //
        array('integral',0), //
        array('integral_money',0), //
        array('bonus',0), //
        array('order_amount',0), //
        array('from_ad',0), //
        array('referer','mobile'), //
        array('shipping_time',0), //
        array('pack_id',0), //
        array('card_id',0), //
        array('bonus_id',0), //
        array('extension_id',0), //
        array('agency_id',0), //
        array('tax',0), //
        array('is_separate',0), //
        array('parent_id',0), //
        array('discount',0), //
        array('country',1), //
        array('inv_type',0), //
    );
    function getTime(){
        return time()+1800;//半个小时
    }
   public function getAll($where){
       $page=I('p',1);
       $where['user_id']=$this->user['user_id'];
       $list =$this->where($where)->order('order_id DESC')->page($page.','.CommonModel::LIMIT)->select();
       $data=$this->getAlls($list);
       return $data;
    }

    /**
     * 待付款
     */
    public function wpay(){
        return $this->getData(OrderModel::NOPAY);
    }
    /**
     * //待发货
     */
    public function wsend(){
        return $this->getData(OrderModel::WSEND);
    }
    /**
     * /待收货
     */
    public function wget(){
        $data=array();
        $result=$this->getData(OrderModel::WGET);
        foreach($result as $k=>$v){
            $order_id=$v['order_id'];
            $result[$k]['order_id'];
        }
        return $this->getData(OrderModel::WGET);
    }
    /**
     * /待评论
     */
    public function wcomment(){
        return $this->getData(OrderModel::WCOMMENT);
    }
    /**
     * 退货
     */
    public function back(){
        return $this->getData(OrderModel::BACK);
    }
    /**
     * 获取订单数据
     * @param $type 根据类型获取
     * @return int|null
     */
    private function getData($type){
        if(!$this->user){
            return 0;
        }
        $data=null;
        $where= $this->getWhere($type);
        if($where){
            $res=$this->getAll($where);
            $data=$this->getInfo($res);
        }
        return $data;
    }

    /**
     * 获取某种订单状态的信息
     * @param $res
     * @return array
     */
    private function getInfo($res){
        $info=array();
        $brand=array();
        foreach($res as  $k=>$v){
            $info[$k]['order_id']=$v['order_id'];
            $info[$k]['shipping_fee']=$v['shipping_fee'];
            $info[$k]['surplus']=$v['surplus'];
            $info[$k]['invoice_no']=$v['invoice_no'];
            $order_id['order_id']=$v['order_id'];
            $list=$this->order_goods->alias('c')->join('LEFT JOIN __GOODS__ g on g.goods_id = c.goods_id')
                ->field('c.goods_id,c.goods_number,c.goods_name,c.goods_price,c.goods_attr,g.goods_thumb')
            ->where($order_id)->select();
            foreach($list as $key=>$val){
                $id=$val['goods_id'];
                $res=M('Goods')->alias('g')->join('LEFT JOIN __BRAND__ b on g.brand_id = b.brand_id')
                    ->field('g.*,b.*')->where(array('goods_id'=>$id))->find();
                $brand_id=$res['brand_id'];
                $info[$k]['brand'][$brand_id]['brand_logo']=$res['brand_logo'];
                $info[$k]['brand'][$brand_id]['brand_name']=$res['brand_name'];
                $info[$k]['brand'][$brand_id]['goods'][]=$val;
            }
        }
        foreach($info as $ky=>$va){
            $brand[$ky]['order_id']=$va['order_id'];
            $brand[$ky]['shipping_fee']=$va['shipping_fee'];
            $brand[$ky]['surplus']=$va['surplus'];
            $brand[$ky]['order_id']=$va['order_id'];
            $brand[$ky]['invoice_no']=$va['invoice_no'];
            foreach($va['brand'] as $bk=>$bv){
                $brand[$ky]['brand'][]=$bv;
            }
        }
        return $brand;
    }

    /**
     * 获取所有订单信息
     * @param $res
     * @return array
     */
    private function getAlls($res){
        $info=array();
        $brand=array();
        foreach($res as  $k=>$v){
            $info[$k]['order_id']=$v['order_id'];
            $info[$k]['shipping_fee']=$v['shipping_fee'];
            $info[$k]['surplus']=$v['surplus'];
            $info[$k]['invoice_no']=$v['invoice_no'];
            $info[$k]['order_status']=$v['order_status'];
            $info[$k]['pay_status']=$v['pay_status'];
            $info[$k]['shipping_status']=$v['shipping_status'];
            $order_id['order_id']=$v['order_id'];
            $list=$this->order_goods->alias('c')->join('LEFT JOIN __GOODS__ g on g.goods_id = c.goods_id')
                ->field('c.goods_id,c.goods_number,c.goods_name,c.goods_attr,c.goods_price,g.goods_thumb')
                ->where($order_id)->select();
            foreach($list as $key=>$val){
                $id=$val['goods_id'];
                $res=M('Goods')->alias('g')->join('LEFT JOIN __BRAND__ b on g.brand_id = b.brand_id')
                    ->field('g.*,b.*')->where(array('goods_id'=>$id))->find();
                $brand_id=$res['brand_id'];
                $info[$k]['brand'][$brand_id]['brand_logo']=$res['brand_logo'];
                $info[$k]['brand'][$brand_id]['brand_name']=$res['brand_name'];
                $info[$k]['brand'][$brand_id]['goods'][]=$val;
            }
        }
        foreach($info as $ky=>$va){
            $brand[$ky]['order_id']=$va['order_id'];
            $brand[$ky]['shipping_fee']=$va['shipping_fee'];
            $brand[$ky]['surplus']=$va['surplus'];
            $brand[$ky]['order_id']=$va['order_id'];
            $brand[$ky]['invoice_no']=$va['invoice_no'];
            if($va['order_status']==0 &&$va['pay_status']==0){
                $brand[$ky]['type']=OrderModel::NOPAY;//未付款
            }elseif($va['order_status']==1 &&$va['pay_status']==2 &&$va['shipping_status']==0){
                $brand[$ky]['type']=OrderModel::WSEND;//待发货
            }elseif($va['order_status']==5 &&$va['pay_status']==2 &&$va['shipping_status']==1){
                $brand[$ky]['type']=OrderModel::WGET;//待收货
            }elseif($va['shipping_status']==2){
                $brand[$ky]['type']=OrderModel::WCOMMENT;//待评价
            }
            foreach($va['brand'] as $bk=>$bv){
                $brand[$ky]['brand'][]=$bv;
            }
        }
        return $brand;
    }
    public function getWhere($type){
        if(!$this->user){
            return 0;
        }
        $where=null;
       switch($type){
           case OrderModel::NOPAY://待支付
               $where['order_status']=0;
               $where['pay_status']=0;
               break;
           case OrderModel::WSEND://待发货
               $where['order_status']=1;
               $where['pay_status']=2;
               $where['shipping_status']=0;
               break;
           case OrderModel::WGET://待收货
               $where['order_status']=5;
               $where['pay_status']=2;
               $where['shipping_status']=1;
               break;
           case OrderModel::WCOMMENT://待评价
               $where['shipping_status']=2;
               break;
           case OrderModel::BACK://退货
               $where['order_status']=4;
               $where['pay_status']=0;
               $where['shipping_status']=0;
               break;
       }
        return $where;
    }
    public function getOne($id){
        $info=array();
        $where=array(
            'i.order_id'=>$id,
            'user_id'=>$this->user['user_id'],
            'order_status'=>0,
            'pay_status'=>0
        );
        $result=$this->alias('i')->join('LEFT JOIN __ORDER_GOODS__ g on g.order_id = i.order_id')
            ->field('i.surplus,i.order_sn,i.pay_name,g.goods_name')->where($where)->select();
        if($result){
            $info['out_trade_no']=$result['0']['order_sn'];
            $info['subject']=$result['0']['goods_name'];
            $info['body']=$result['0']['goods_name'];
            $info['total_fee']=$result['0']['surplus'];
        }
        return $info;
    }
}