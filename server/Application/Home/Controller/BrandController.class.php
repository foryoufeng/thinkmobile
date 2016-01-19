<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/22
 * Time: 10:15
 */

namespace Home\Controller;


use Common\Controller\CommonController;

class BrandController extends CommonController
{
    public function _initialize(){
        $this->model=D('Brand');
    }
    
}