<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/18
 * Time: 10:26
 */

namespace Admin\Controller;


class OrderController extends CommonController
{
    public function _initialize(){
        $this->model=D('Order');
    }
}