<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/25
 * Time: 15:40
 */

namespace Common\Util;
use Common\Util\Alipay\AlipaySubmit;

class AlipayUtil
{
       public static function getUrl($data){
           $submit=new AlipaySubmit();
           return $submit->url($data);
       }
}