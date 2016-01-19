<?php
namespace Home\Controller;
use Common\Controller\CommonController;
use Common\Util\WeiUtil;
class UserController extends CommonController {
	private $user;
	function _initialize(){
		parent::_initialize();
		$this->model=D('User');
	}
//	授权登录
    public function autoLogin(){
		$data=$this->model->autoLogin();
        echo $data;
	}
    public function index(){
		$data=$this->model->getAll();
		if($data){
			ajax($data);
		}else{
			ajax(0);
		}
    }
	//	填写完整的信息
	public function address(){
		$address=D("Address");
		$user=session('user');
		$res=$address->getOne($user['id']);
		$add=I("add",'a');
		if(IS_POST){
            if($res){//有就更新
				echo $address->editData($res['id']);
			}else{//没有就添加
				echo $address->addData();
			}
			if($add=='add'){
				header("Location:".U("Index/pay"));
			}else{
				header("Location:".U("User/ok"));
			}
		}else{
			$this->assign('address',$res);
			$this->assign('add',$add);
			$this->display();
		}
	}

	/**
	 * 收货地址编辑成功
	 */
	public function ok(){
		$this->assign('url',U("User/center"));
		$this->display();
	}

	/**
	 * 用户中心页
	 */
	public function center(){
		$res=$this->model->getOne(array('id'=>session('user.id')));
		$this->assign('point',$res['jifen']);
		$this->assign('url',U("User/center"));
		$this->display();
	}

	/**
	 * 用户登录
	 */
	public function login(){
		$url=I('url');
		if(session('user')){
			$data['url']=$url;
			$data['id']=session('user.user_id');
			$data['auth']=session('user.auth');
			ajax($data);
		}
		$res=$this->model->login();
		if($res){
			session(array('name'=>'session_id','expire'=>3600));
			session('user',$res);
			$data['url']=$url;
			$data['id']=session('user.user_id');
			$data['auth']=session('user.auth');
		}else{
			$data=session('user.user_id');
		}
		ajax($data);
	}

	/**
	 * 用户注册
	 */
	public function register(){

	}
	/**
	 * 用户退出
	 */
	public function logout(){
		session('user',null); // 删除session
		ajax(1);
	}

}