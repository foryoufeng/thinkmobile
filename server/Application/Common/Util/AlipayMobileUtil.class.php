<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/25
 * Time: 15:40
 */

namespace Common\Util;


use Common\Util\AlipayMobile\AlipaySubmit;

class AlipayMobileUtil
{
       public static function getUrl($data){
           $submit=new AlipaySubmit();
           return $submit->url($data);
       }
}