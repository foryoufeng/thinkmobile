<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/22
 * Time: 10:15
 */

namespace Home\Controller;


use Common\Controller\CommonController;
use Home\Model\CartModel;

class CartController extends CommonController
{
    public function _initialize(){
        $this->model=D('Cart');
    }

    /**
     * 获取商品属性信息
     */
    public function getInfo(){
        $data=$this->model->getInfo();
        $this->msg($data);
    }

    /**
     * 加入购物车
     */
    public function add(){
        if($this->isLogin()){
           $data=$this->model->addData();
            $this->msg($data);
        }
    }

    /**
     * 编辑购物车
     */
    public function edit(){
        if($this->isLogin()) {
            $res = $this->model->editCart();
            $this->msg($res);
        }
    }
    /**
     * 删除购物车中的指定商品
     * @return mixed
     */
    public function delete(){
        if($this->isLogin()) {
            $res = $this->model->del();
            $this->msg($res);
        }
    }
}