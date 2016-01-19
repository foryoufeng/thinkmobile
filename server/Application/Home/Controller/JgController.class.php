<?php
namespace Home\Controller;
use Think\Controller;
class JgController extends Controller {
//	定义私有方法
	private $jg;
	public function __construct(){
		parent::__construct();
		$this->jg=D('Jg');
		$this->model=D('Jg');
	}

//	填写完整的信息
    public function index(){
		$where=array(
			'user_id'=>session('user.id'),
		);
		$count=$this->jg->where($where)->count();
		$this->assign("count",$count);
//  	如果有提交
		if(IS_POST){
//			查询条件			
			$where = array(
				'tel'  => $_POST['tel'],
				'user_id'=>session('user.id')
			);
//			查询符合条件的数据
			$data = $this->jg->where($where)->order('id DESC')->limit(5)->select();
//			如果查询到数据
			if($data == TRUE){
				$this->assign('data',$data);//返回数据
			}else{//如果没有数据
				echo "<script type='text/javascript'>alert('没有查询到相关信息');</script>";//提示
			}
		}
//		载入模板
    	$this->display();
    }
	public function log(){
		$res=$this->model->getAll();
		$page=$this->model->getPage();
		$this->assign('log',$res);
		$this->assign('page',$page);
		$this->display();
	}
}