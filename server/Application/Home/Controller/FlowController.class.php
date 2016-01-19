<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/22
 * Time: 10:15
 */

namespace Home\Controller;


use Common\Controller\CommonController;

class FlowController extends CommonController
{
    public function _initialize(){
        $this->model=D('Flow');
        $this->isLogin();
    }
    public function generate(){
        $data=$this->model->generate();
        $this->msg($data);
    }
}