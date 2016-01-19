<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/14
 * Time: 16:11
 */

namespace Home\Controller;
use Common\Controller\CommonController;
use Common\Model\CommonModel;
use Common\Util\AlipayMobileUtil;
use Common\Util\WeiUtil;
use Common\Util\Weixin\PayNotifyCallBack;
use Home\Model\OrderModel;
use Home\Model\PointlogModel;
use Think\Log;

/**
 * 用户支付操作管理类
 * Class PayController
 * @package Home\Controller
 */
class PayController extends CommonController
{
    private $order;
    function _initialize(){
        $this->order=D('Order');//订单model
        parent::_initialize();
    }

    /**
     * 判断用户是否有收获地址和积分是否足够
     */
      public function index(){
             $id=I('id',0);
             $result=$this->order->getOne($id);
             $json['code']=0;
             $json['msg']='fail';
             if($result){
                 $json['code']=1;
                 $msg=AlipayMobileUtil::getUrl($result);
                 $json['msg']=$msg;
             }
            $this->msg($json);
      }

    /**
     * 支付通知页面
     */
    public function notify(){
        $notify=new PayNotifyCallBack();
        $notify->Handle(false);
    }

}