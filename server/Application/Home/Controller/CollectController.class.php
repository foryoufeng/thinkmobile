<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/22
 * Time: 10:15
 */

namespace Home\Controller;


use Common\Controller\CommonController;

class CollectController extends CommonController
{
    public function _initialize(){
        $this->model=D('Collect');
    }
    //�ҵ��ղؽӿ�
    public function index(){
        $data=$this->model->getAll();
        $this->msg($data);
    }
    //添加收藏
    public function add(){
        $data=$this->model->addData();
        $this->msg($data);
    }
    //删除收藏
    public function delete(){
        $data=$this->model->del();
        $this->msg($data);
    }
    //我的购物车编辑页面移至收藏
    public function moveto(){
        $data=$this->model->move();
        $this->msg($data);
    }


}