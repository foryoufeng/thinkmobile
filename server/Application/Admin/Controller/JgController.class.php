<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Upload;

class JgController extends CommonController {
//	定义私有方法
	private $jg;
	public function __construct(){
		parent::__construct();
		$this->jg=D('Jg');
	}
	
//	调取加工数据
    public function index(){
//  	如果用户搜索产品
		if(IS_POST){
			$where['tel'] = array('like',"%$_POST[tel]%");
//			数据分页开始--------------------------
			$Jg = M('Jg'); // 实例化User对象
			$count      = $Jg->where($where)->count();// 查询满足要求的总记录数
			$Page       = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(15)
			$show       = $Page->show();// 分页显示输出
			$data = $Jg->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
			if($data == FALSE){
				$this->error("没有搜到任何新闻，请更换关键词!");
				die;
			}
//			数据分页结束--------------------------
			$this->assign('data',$data);//分配数据
			$this->assign('page',$show);// 赋值分页输出
		}else{
//			数据分页开始--------------------------
			$Jg = M('Jg'); // 实例化User对象
			$count      = $Jg->count();// 查询满足要求的总记录数
			$Page       = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(15)
			$show       = $Page->show();// 分页显示输出
			$data = $Jg->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
			if($data == FALSE){
				$this->error("没有添加任何产品，请添加产品!",U("Admin/Cp/add"));
				die;
			}
//			数据分页结束--------------------------
			$this->assign('data',$data);//分配数据
			$this->assign('page',$show);// 赋值分页输出
		}
		$this->display();//载入模板
    }
	
	
//	添加加工
    public function add(){
//  	如果提交
		if(IS_POST){
//			添加图片开始
		    $upload = new Upload();// 实例化上传类
		    $upload->maxSize   =     3145728 ;// 设置附件上传大小
		    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		    $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
		    $upload->savePath  =     ''; // 设置附件上传（子）目录
		    // 上传文件 
		    $info   =   $upload->upload();
		    $_POST['img'] = "/Uploads/".$info['img1']['savepath'].$info['img1']['savename'];	
//			添加图片结束
//			添加产品到数据库开始-----------
			$res = $this->jg->add($_POST);
			if($res == TRUE){
				$this->success("添加成功!",U("Admin/Jg/index"));
				die;
			}else{
				$this->error("添加失败!");
				die;
			}
//			添加产品到数据库结束-----------		
		}else{
			$user_id=I('id',0);
			$this->assign('user_id',$user_id);
			$this->display();
		}

    }


//	删除加工方法
	public function del(){
//		删除条件。get上面会给我一个id值
		$where = array(
			'id' => $_GET['id'],
		);
//		查找要删除的数据并删除
		$res = $this->jg->where($where)->delete();
		if($res == TRUE){//删除成功
			$this->success("删除成功!",U("Admin/Jg/index"));//跳转
			die;
		}else{//删除失败
			$this->error("添加失败!");//报错
			die;
		}
	}
	
//	修改加工的方法
	public function save(){
		$where['id']=I('id',0);
//		首先获取数据开始-------------------
		$data = $this->jg->where($where)->find();//根据条件获取数据
		$this->assign("data",$data);//分配数据
//		首先获取数据结束-------------------
//		判断是否有提交
//  	如果提交
		if(IS_POST){
//			添加图片开始
		    $upload = new Upload();// 实例化上传类
		    $upload->maxSize   =     3145728 ;// 设置附件上传大小
		    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		    $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
		    $upload->savePath  =     ''; // 设置附件上传（子）目录
		    // 上传文件 
		    $info   =   $upload->upload();
			if($info){
		    	$_POST['img'] = "/Uploads/".$info['img1']['savepath'].$info['img1']['savename'];	
			}
//			添加图片结束
//			添加产品到数据库开始-----------
			$res = $this->jg->save($_POST);
			if($res == TRUE){
				$this->success("修改成功!",U("Admin/Jg/index"));
				die;
			}else{
				$this->error("修改失败!");
				die;
			}
//			添加产品到数据库结束-----------		
		}
		$this->display();//载入模板
	}
	
	
	
}