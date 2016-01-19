<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/22
 * Time: 10:14
 */

namespace Home\Model;


use Common\Model\CommonModel;

class CartModel extends CommonModel
{
    private  $product;//产品模型
    private  $goods;//商品模型
    private  $brand;//商品模型
    private  $goods_attr;//商品属性模型
    private  $attr;//属性模型
    private  $user_id;//用户id
    private  $success=array('code'=>0,'msg'=>'success');//库存不足
    private  $nonum=array('code'=>1,'msg'=>'number not enough');//库存不足
    private  $noattrid=array('code'=>2,'msg'=>'no attr_id');//有商品属性没有传商品属性id
    private  $fail=array('code'=>3,'msg'=>'fail');//加入购物车失败
    const INC='inc';
    const DEC='dec';
    protected $_auto = array (
        array('is_real',1),  // 新增的时候把status字段设置为1
        array('parent_id',0) ,
        array('rec_type',0),
        array('is_gift',0),
        array('can_handsel',0),
    );
    protected $_validate = array(
        array('user_id','require','用户id必须！'), //默认情况下用正则进行验证
        array('session_id','require','session_id'), // 在新增的时候验证name字段是否唯一
        array('goods_id','require','goods_id'),
        array('product_id','require','product_id'),
        array('goods_name','require','goods_name'),
        array('goods_price','require','goods_price'),
        array('goods_number','require','goods_number'),
    );
    public function _initialize(){
        $this->product=M('products');
        $this->goods_attr=M('goods_attr');
        $this->goods=M('goods');
        $this->attr=M('attribute');
        $this->user_id=session('user.user_id');
    }

    /**
     * 获取购物车中的信息
     * @return array
     */
    public function getAll(){
        $list=null;
        $result=array();
        $where['user_id']=$this->user_id;
        $data=$this->alias('c')->join('LEFT JOIN __GOODS__ g on g.goods_id = c.goods_id')
            ->field('c.goods_id,c.goods_number,c.goods_name,c.goods_attr,c.goods_price,c.rec_id,c.product_id,g.goods_thumb')
            ->where($where)->select();
        foreach($data as $k=>$v){
            $id=$v['goods_id'];
            $res=$this->goods->alias('g')->join('LEFT JOIN __BRAND__ b on g.brand_id = b.brand_id')
                ->field('g.*,b.*')->where(array('goods_id'=>$id))->select();
            $brand_id=$res[0]['brand_id'];
            $list['brand'][$brand_id]['brand_logo']=$res[0]['brand_logo'];
            $list['brand'][$brand_id]['brand_name']=$res[0]['brand_name'];
            $list['brand'][$brand_id]['brand_id']=$brand_id;
            $list['brand'][$brand_id]['goods'][]=$v;
        }
        foreach($list['brand']  as $k=>$v){
            $result['brand'][]=$v;
        }
        return $result;
    }
    /**
     * 获取商品属性的库存和价格
     * @return array|null
     */
    public function getInfo(){
           $data=null;
           $goods_id=I('goods_id',0);
           $attr_id=I('attr_id',0);
           $data=$this->proinfo($goods_id,$attr_id);
           return $data;
    }

    /**
     * 获取商品的库存和价格
     * @param $goods_id 商品id
     * @param $goods_attr 商品的属性
     * @return array 商品的库存和价格
     */
    private function proinfo($goods_id,$goods_attr){
        $return_array = array();
        //获取商品库存信息
        $return_array['number']=$this->getNumber($goods_id,$goods_attr);
        //获取商品的价
        $return_array['price']=$this->getPrice($goods_id,$goods_attr);
        //价格结束
        return $return_array;
    }
    /**
     * 加入购物车
     */
    public function addData(){
        $goods_id=I('goods_id',0);
        $attr_id=I('attr_id');
        $number=I('number',1);
        $data=$this->addCart($goods_id,$attr_id,$number);
        return $data;
    }
    private function addCart($goods_id,$attr_id,$number){
        $data=$this->getAttr($goods_id);//获取商品属性
        if($data && !$attr_id){//有商品属性没有传商品属性id
          return $this->noattrid;//没有商品
        }
        $result=$this->getCart($goods_id,$attr_id);
        $amount=$this->getNumber($goods_id,$attr_id);//获取库存
        if($result){//购物车中存在商品就更新
            if(intval($number)+intval($result['goods_number'])>$amount){
                return $this->nonum;//库存不足
            }
           $res=$this->addNum($goods_id,$attr_id,$number);
           if($res){
               return $this->success;//添加成功
           }else{
               return $this->fail;//添加失败
           }
        }else{//没有就加入购物车
            $amount=$this->getNumber($goods_id,$attr_id);

            if(intval($number)>$amount){
                return $this->nonum;//库存不足
            }
            $info=array(
                'user_id'=>$this->user_id,
                'session_id'=>session_id(),
                'goods_id'=>$goods_id,
                'goods_attr_id'=>$attr_id,
                'goods_number'=>$number,
                'goods_price'=>$this->getPrice($goods_id,$attr_id)
            );
            $where['goods_id']=$goods_id;
            $ginfo=$this->goods->field('goods_sn,market_price,shop_price,goods_name')->where($where)->find();//获取商品信息
            $product=$this->getProduct($goods_id,$attr_id);
            $datas=array_merge($info,$ginfo,$product);
            $datas['goods_attr']=$this->getAttrName($goods_id,$attr_id);
            $res=parent::addData($datas);
            if($res==CommonModel::MSUCCESS){
                return $this->success;//添加成功
            }else{
                return $this->fail;//添加失败
            }
        }
    }
    protected function getAttrName($goods_id,$goods_attr){
        $info=null;
        $attr=explode(',',$goods_attr);
        $where['g.goods_id']=$goods_id;
        $where['a.attr_type']=1;
        $where['g.goods_attr_id']=array('in',$attr);
        $data=$this->goods_attr->alias('g')->join('LEFT JOIN __ATTRIBUTE__ a on g.attr_id = a.attr_id')
            ->field('attr_value,attr_name')->where($where)->select();
        foreach($data as $k=>$v){
            $info.=$v['attr_name'].':';
            $info.=$v['attr_value'].' ';
        }
        return $info;
    }
    /**
     * 获取商品库存
     * @param $goods_id 商品id
     * @param $goods_attr 商品属性
     * @return int 库存数
     */
    public function getNumber($goods_id,$goods_attr){
        $attr='%'.implode('|',explode(',',$goods_attr)).'%';
        $attr2='%'.implode('|',array_reverse(explode(',',$goods_attr))).'%';
        $where['goods_attr']=array('like',array($attr,$attr2),'or');
        $where['goods_id']=$goods_id;
        $number=$this->product->where($where)->sum('product_number');//商品库存
        return $number?$number:0;
    }

    /**
     * 获取商品的价格
     * @param $goods_id 商品id
     * @param $goods_attr 商品属性
     * @return mixed 商品的价格
     */
    public function getPrice($goods_id,$goods_attr){
        $attr_id=explode(',',$goods_attr);
        $where['goods_id']=$goods_id;
        $row=$this->goods->field('shop_price')->where($where)->find();
        $shop_price=$row['shop_price'];
        $where['goods_attr_id']=array('in',$attr_id);
        $add_price=$this->goods_attr->where($where)->sum('attr_price');//增加的价格
        $price=$shop_price+$add_price;
        return $price;
    }

    /**
     * 根据商品id获取商品属性
     * @param $goods_id 根据商品id获取商品属性
     * @return mixed 商品的信息
     */
    public function getAttr($goods_id){
        $where['g.goods_id']=$goods_id;
        $where['a.attr_type']=1;
        $data=$this->goods_attr->alias('g')->join('LEFT JOIN __ATTRIBUTE__ a on g.attr_id = a.attr_id')
            ->where($where)->select();
        return $data;
    }

    /**
     * 根据商品id和商品属性id来获取购物车中的商品信息
     * @param $goods_id 商品的id
     * @param $goods_attr 商品的属性
     * @return mixed 购物车中商品的信息 | null
     */
    public function getCart($goods_id,$goods_attr){
        $where['goods_id']=$goods_id;
        $attr2=implode(',',array_reverse(explode(',',$goods_attr)));
        $where['goods_attr_id']=array('like',array($goods_attr,$attr2),'or');
        $where['user_id']=$this->user_id;
        $data=$this->where($where)->find();
        return $data;
    }

    /**
     * 根据商品信息来添加购物车中已存在的商品
     * @param $goods_id
     * @param $attr_id
     * @param $number
     * @return bool
     */
    public function addNum($goods_id,$attr_id,$number){
        $where=array(
            'user_id'=>$this->user_id,
            'goods_id'=>$goods_id,
            'goods_attr_id'=>$attr_id
        );
        $res=$this->where($where)->setInc('goods_number',$number);
        return $res;
    }

    /**
     * 根据商品信息来减少购物车中已存在的商品
     * @param $goods_id
     * @param $attr_id
     * @param $number
     * @return bool
     */
    public function decNum($goods_id,$attr_id,$number){
        $where=array(
            'user_id'=>$this->user_id,
            'goods_id'=>$goods_id,
            'goods_attr_id'=>$attr_id
        );
        $res=$this->where($where)->setDec('goods_number',$number);
        return $res;
    }

    /**
     * 获取产品信息
     * @param $goods_id
     * @param $goods_attr
     * @return mixed
     */
    protected function getProduct($goods_id,$goods_attr){
        $attr='%'.implode('|',explode(',',$goods_attr)).'%';
        $attr2='%'.implode('|',array_reverse(explode(',',$goods_attr))).'%';
        $where['goods_attr']=array('like',array($attr,$attr2),'or');
        $where['goods_id']=$goods_id;
        $data=$this->product->where($where)->find();//产品信息
        return $data;
    }

    /**
     * 修改购物车
     * @return bool|int
     */
    public function editCart(){
        $where['rec_id']=I('rec_id',0);
        $number=1;
        $flag=I('flag',CartModel::INC);
        $proid=I('product_id',0);
        $res=0;
        $num=$this->where($where)->field('goods_number')->find();
        if($flag==CartModel::INC){
            $all=$this->product->where(array('product_id'=>$proid))->field('product_number')->find();
            if($num['goods_number']+$number>$all['product_number']){
                return 2;//库存不足
            }
            $res= $this->where($where)->setInc('goods_number',$number);
        }else if($flag==CartModel::DEC){
            if($num['goods_number']<=1){
                return $res;
            }
            $res= $this->where($where)->setDec('goods_number',$number);
        }
        return $res;
    }

    /**
     * 删除购物车中的指定商品
     * @return mixed
     */
    public function del(){
        $where=array(
            'user_id'=>$this->user_id,
            'rec_id'=>I('rec_id',0),
        );
        $res=$this->where($where)->delete();
        return $res;
    }
}