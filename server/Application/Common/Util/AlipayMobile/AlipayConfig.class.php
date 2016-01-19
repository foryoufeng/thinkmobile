<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/25
 * Time: 16:04
 */

namespace Common\Util\AlipayMobile;


class AlipayConfig
{

     const PARTNER='#';//合作身份者id，以2088开头的16位纯数字
     const ALIURL='https://mapi.alipay.com/gateway.do?';//合作身份者id，以2088开头的16位纯数字

     const SENDER='#';//收款支付宝账号，一般情况下收款账号就是签约账号

     const  KEY='#';//安全检验码，以数字和字母组成的32位字符

     const RSA='RSA';//签名方式 不需修改
     //支付宝公钥（后缀是.pen）文件相对路径
     const PUBLIC_KEY='./key/alipay_public_key.pem';
     //商户的私钥（后缀是.pem）文件的绝对路径
     const PRIVATE_KEY='/Application/Common/Util/AlipayMobile/key/rsa_private_key.pem';
     const CHARSET='utf-8';//字符编码格式 目前支持 gbk 或 utf-8
     //ca证书路径地址，用于curl中ssl校验
     //请保证cacert.pem文件在当前文件夹目录中
      const TRANSPORT='http';
     const NOTIFY_URL='#';
}