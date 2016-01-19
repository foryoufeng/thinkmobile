<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/16
 * Time: 16:16
 */

namespace Home\Controller;
use Common\Controller\CommonController;
use Common\Util\AlipayMobileUtil;

class OrderController extends CommonController
{
    function _initialize(){
        parent::_initialize();
        $this->model=D('Order');
    }
    public function index(){
        $data=$this->model->getAll();
        $this->msg($data);
    }
    //待支付
    public function  wpay(){
        $data=$this->model->wpay();
        $this->msg($data);
    }
    //待发货
    public function  wsend(){
        $data=$this->model->wsend();
        $this->msg($data);
    }
    //待收货
    public function  wget(){
        $data=$this->model->wget();
        $this->msg($data);
    }
    //待评价
    public function  wcomment(){
        $data=$this->model->wcomment();
        $this->msg($data);
    }
    //待评价
    public function  back(){
        $data=$this->model->back();
        $this->msg($data);
    }
    public function geturl(){
        $money=0.01;
        $data=array(
            "out_trade_no"	=> time(),//商户网站唯一订单号
            "subject"	=> 'test',//商品名称
            "total_fee"	=> $money,//费用  正常人民币  而不是像微信那样的100/1
            "body"	=> 'test',//商品描述
        );
        //echo "正在跳转支付宝....<script> window.location.href='".AlipayMobileUtil::getUrl($data)."'</script>";
        echo AlipayMobileUtil::getUrl($data);
    }
}