<?php
namespace Admin\Controller;
use Think\Controller;
class XwController extends CommonController{
//	定义私有方法
	public function _initialize(){
		$this->model=D('Xw');
	}
}