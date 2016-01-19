<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/25
 * Time: 16:04
 */

namespace Common\Util\AlipayMobile;

class AlipaySubmit
{
     private $parameter;
     function  __construct(){
          $this->parameter=array(
              'service'        => 'mobile.securitypay.pay',
              "partner" => trim(AlipayConfig::PARTNER),//合作身份者id
              "seller_id" => trim(AlipayConfig::SENDER),//收款支付宝账号
              "payment_type"	=> 1,//支付类型  1  商品购买   只能为1
              "notify_url"	=> AlipayConfig::NOTIFY_URL,//通知地址
              "_input_charset"	=> trim(strtolower(AlipayConfig::CHARSET)),//参数编码字符集
          );
     }

     /**
      * 根据提交的参数来组装支付宝所需的字符串
      * @param $data
      * @return 请求的数组
      */
     function buildRequestPara($data){
          $this->parameter=array_merge($this->parameter,$data);
          //除去待签名参数数组中的空值和签名参数
          $para_filter = AlipayCore::paraFilter($this->parameter);

          //对待签名参数数组排序
          $para_sort = AlipayCore::argSort($para_filter);
          //生成签名结果
          $mysign = $this->buildRequestMysign($para_sort);
          //签名结果与签名方式加入请求提交参数组中
          $para_sort['sign'] = $mysign;
          $para_sort['sign_type'] = strtoupper(trim(AlipayConfig::RSA));
          return $para_sort;
     }
     /**
      * 生成签名结果
      * @param $para_sort 已排序要签名的数组
      * return 签名结果字符串
      */
     private function buildRequestMysign($para_sort) {
          //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
          $prestr = AlipayCore::createLinkstring($para_sort);

          $mysign = AlipayCore::rsaSign($prestr,AlipayConfig::PRIVATE_KEY);

          return $mysign;
     }
     /**
      * 生成要请求给支付宝的支付url
      * @param $para_temp 请求前的参数数组
      * @return 要请求的参数数组字符串
      */
     function url($para_temp) {
          //待请求参数数组
          $para = $this->buildRequestPara($para_temp);

          //把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
          $request_data = AlipayCore::createLinkstringUrlencode($para);
          //return AlipayConfig::ALIURL.$request_data;
          return $request_data;
     }
}