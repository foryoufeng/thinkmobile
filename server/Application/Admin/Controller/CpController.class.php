<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Upload;

class CpController extends CommonController {
//	定义私有方法
	private $sp;
	public function __construct(){
		parent::__construct();
		$this->sp=D('Sp');
	}


//	显示产品页面	
    public function index(){
//  	如果用户搜索产品
		if(IS_POST){
			$where['title'] = array('like',"%$_POST[title]%");
//			数据分页开始--------------------------
			$Sp = M('Sp'); // 实例化User对象
			$count      = $Sp->where($where)->count();// 查询满足要求的总记录数
			$Page       = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(15)
			$show       = $Page->show();// 分页显示输出
			$data = $Sp->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
			if($data == FALSE){
				$this->error("没有搜到任何新闻，请更换关键词!");
				die;
			}
//			数据分页结束--------------------------
			$this->assign('data',$data);//分配数据
			$this->assign('page',$show);// 赋值分页输出
		}else{
//			数据分页开始--------------------------
			$Sp = M('Sp'); // 实例化User对象
			$count      = $Sp->count();// 查询满足要求的总记录数
			$Page       = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(15)
			$show       = $Page->show();// 分页显示输出
			$data = $Sp->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
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


//	添加产品页面	
    public function add(){
//  	如果提交
		if(IS_POST){
//			判断产品编号是否重复开始-------
			$where  = array(
				'bh'  => $_POST['bh'],
			);
			$bh=$this->sp->where($where)->find();
			if($bh == true){
				$this->error("产品编号不能重复!");
				die;
			}
//			判断产品编号是否重复结束-------
			$upload=new Upload();
			$upload->maxSize   =     3145728 ;// 设置附件上传大小
			$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
			$upload->savePath  =     ''; // 设置附件上传（子）目录
			// 上传文件
			$info   =   $upload->upload();
			$_POST['content']= "/Uploads/".$info['content']['savepath'].$info['content']['savename'];
//			添加产品到数据库开始-----------
			$res = $this->sp->add($_POST);
			if($res == TRUE){
				$this->success("添加成功!",U("Admin/Cp/index"));
				die;
			}else{
				$this->error("添加失败!");
				die;
			}
//			添加产品到数据库结束-----------		
		}
    	$this->display();
    }


//	删除产品方法
	public function del(){
//		删除条件。get上面会给我一个id值
		$where = array(
			'id' => $_GET['id'],
		);
//		查找要删除的数据并删除
		$res = $this->sp->where($where)->delete();
		if($res == TRUE){//删除成功
			$this->success("删除成功!",U("Admin/Cp/index"));//跳转
			die;
		}else{//删除失败
			$this->error("添加失败!");//报错
			die;
		}
	}


//	修改产品的方法
	public function save(){
//		首先获取数据开始-------------------
		$where = array(
			'id'  => $_GET['id'],
		);
		$data = $this->sp->where($where)->find();//根据条件获取数据
		$this->assign("data",$data);//分配数据
//		首先获取数据结束-------------------
//		判断是否有提交
		if(IS_POST){
//			判断产品编号是否重复开始-------
			$tiaojian['bh'] = $_POST['bh'];//条件 bh不能相同
			$tiaojian['id'] = array('not in',"$_GET[id]");//跳过本条查询
			$bh=$this->sp->where($tiaojian)->find();
			if($bh == true){
				$this->error("产品编号不能重复!");
				die;
			}
//			判断产品编号是否重复结束-------
			$upload=new Upload();
			$upload->maxSize   =     3145728 ;// 设置附件上传大小
			$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
			$upload->savePath  =     ''; // 设置附件上传（子）目录
			// 上传文件
			$info   =   $upload->upload();
			$_POST['content']= "/Uploads/".$info['content']['savepath'].$info['content']['savename'];
//			添加产品到数据库开始-----------
			$res = $this->sp->where($where)->save($_POST);
			if($res == TRUE){
				$this->success("修改成功!",U("Admin/Cp/index"));
				die;
			}else{
				$this->error("修改失败!");
				die;
			}
//			添加产品到数据库结束-----------		
		}
		$this->display();//载入模板
	}
   public function upload(){
	   $upload=new Upload();
	   $upload->maxSize   =     3145728 ;// 设置附件上传大小
	   $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	   $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
	   $upload->savePath  =     ''; // 设置附件上传（子）目录
	   // 上传文件
	   $info   =   $upload->upload();
	  // echo json_encode($info);
	   $file= "/Uploads/".$info['content']['savepath'].$info['content']['savename'];
//			添加图片结束
      $this->success($file);
   }

}