<?php
namespace Home\Controller;
use Common\Controller\CommonController;

class FocusController extends CommonController {
	public function _initialize(){
		$this->model=D('Focus');
	}
	//��ʾ�ѹ�ע�ӿ�
	public function index(){
		$data=$this->model->tabulation();
		$this->msg($data);
	}
	//��ӹ�ע�ӿ�
	public function add(){
		$data=$this->model->addData();
		$this->msg($data);
	}
	//ȡ����ע�ӿ�
	public function delete(){
		$data=$this->model->delete();
		$this->msg($data);
	}
}