<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;

class UserController extends CommonController {
    public function _initialize(){
        $this->model=D('User');
    }
}