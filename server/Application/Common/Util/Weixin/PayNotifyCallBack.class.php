<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/17
 * Time: 9:52
 */

namespace Common\Util\Weixin;

use Common\Model\CommonModel;
use Home\Model\PointlogModel;
use Think\Log;

class PayNotifyCallBack extends WxPayNotify
{
    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);
        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {
        Log::write(json_encode($data));//记录下支付信息
        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            return false;
        }
        $order=D('Order');
        $order_info=$order->getOne(array('order_id'=>$data['out_trade_no'],'phone'=>$data['attach']));
        if($order_info && $order_info['status']==0){
            $order_info['status']=1;
            $result=$order->editData($order_info);
            if($result==CommonModel::MSUCCESS){
                $point=D('Pointlog');
                $point->log($order_info['user_id'],$order_info['point'],"订单".$order_info['order_id']." 使用了".$order_info['point']."积分",PointlogModel::DEC);
                $money=$order_info['total']/100;
                $addpiont=intval($money/C('DEVPOINT'));
                $point->log($order_info['user_id'],$addpiont,"订单".$order_info['order_id']." 获得了".$addpiont."积分",PointlogModel::ADD);
            }
        }
        return true;
    }
}