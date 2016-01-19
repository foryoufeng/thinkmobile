<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/22
 * Time: 10:15
 */

namespace Home\Controller;


use Common\Controller\CommonController;

class AddressController extends CommonController
{
    public function _initialize(){
        $this->model=D('Address');
    }


    public function add(){
        $data=$this->model->addData();
        $this->msg($data);
    }
    public function province(){
        $data=$this->model->city();
        $this->msg($data);
    }
    public function getOne(){
        $data=$this->model->getOne();
        $this->msg($data);
    }
    public function delete(){
        $data=$this->model->del();
        $this->msg($data);
    }
//

}