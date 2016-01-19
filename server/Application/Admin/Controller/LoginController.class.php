<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {

//	登录方法
	public function index(){
//		判断有没有提交
		if(IS_POST){
//			把提交的密码进行加密
			$_POST["password"] = $_POST['mima'];
//			填写查找条件
			$where = array(
				'user_name'  => $_POST['user'],
			);
//			把查取到的数据赋值
			$psw = D("Admin_user")->field("user_id")->where($where)->find();
//			判断提交的密码和数据库是否相同
			if($psw['user_id'] == $_POST["password"]){//如果相同，就相当于登陆成功
				session('user',$_POST['user']);//设置session
//				成功提示，并且跳转
				$this->success("登录成功,正在跳转!",U("/Admin/Index#1/1"));
				die;
			}else{//否则就是登录失败
				$this->error("用户名或密码不正确!");
				die;
			}
		}
		if($_SESSION['user'] == TRUE){
			header("Location:".__ROOT__."/index.php/Admin/Index#1/1");
		}
		dump($_SERVER);
		$this->display();
	}
	
	
//	退出方法
	public function tuichu(){
		session('user',null); // 删除session
		header("Location:".U('Admin/Login/index'));//跳转到登录页面
	}

}