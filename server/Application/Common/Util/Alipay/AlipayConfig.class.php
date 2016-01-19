<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/25
 * Time: 16:04
 */

namespace Common\Util\Alipay;


class AlipayConfig
{

     const PARTNER='#';//合作身份者id，以2088开头的16位纯数字
     const ALIURL='https://mapi.alipay.com/gateway.do?';//合作身份者id，以2088开头的16位纯数字

     const SENDER='#';//收款支付宝账号，一般情况下收款账号就是签约账号

     const  KEY='#';//安全检验码，以数字和字母组成的32位字符

     const TYPE='MD5';//签名方式 不需修改

     const CHARSET='utf-8';//字符编码格式 目前支持 gbk 或 utf-8
     //ca证书路径地址，用于curl中ssl校验
     //请保证cacert.pem文件在当前文件夹目录中
     const CACERT='cacert.pem';
     //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
     const TRANSPORT='http';
     const NOTIFY_URL='#';
     const RETURN_URL='#';
}