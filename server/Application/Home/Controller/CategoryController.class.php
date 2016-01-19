<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/23
 * Time: 11:07
 */

namespace Home\Controller;


use Common\Controller\CommonController;

class CategoryController extends CommonController
{
    public function _initialize(){
        $this->model=D('Category');
    }
}