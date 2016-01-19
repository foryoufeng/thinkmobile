<?php
namespace Admin\Controller;
use Common\Model\CommonModel;
use Think\Controller;
class CommonController extends Controller{
	protected $model;
	public function __construct(){
		parent::__construct();
		if(!isset($_SESSION['user'])){
			$this->error("你还没有登录!",U('Admin/Login/index'));
			die;
		}
	}

	/**
	 * 根据条件分页显示所有数据
	 */
	public function index(){
		$search=I('search',null);
		$list = $this->model->getAll();
		$show=$this->model->getPage();
		$this->assign('list',$list);
		$this->assign('search',$search);
		$this->assign('page',$show);
		$this->display();
	}

	/**
	 * 根据指定的id删除数据
	 */
	public function delete(){
		$id=I('id',0);
		$res=$this->model->delete($id);
		if($res){
			$this->success("删除成功");
		}else{
			$this->error("删除失败");
		}
	}

	/**
	 * 根据数据进行添加
	 * @param $data 传入的数据，没有就获取post的数据
	 */
	public function add($data=null){
		if(IS_POST) {
			$res = $this->model->addData($data);
			if ($res == CommonModel::MSUCCESS) {
				$this->success("添加成功");
			} else {
				$this->error("添加失败");
			}
		}else{
			$this->display();
		}
	}

	/**
	 * 根据数据进行修改
	 * @param $data 传入的数据，没有就获取post的数据
	 */
	public function save($data=null){
		if(IS_POST) {
			$res = $this->model->editData($data);
			if ($res == CommonModel::MSUCCESS) {
				$this->success("编辑成功");
			} else {
				$this->error("编辑失败");
			}
		}else{
			$id=I('id',0);
			$res = $this->model->getOne(array('id'=>$id));//根据条件获取数据
			$this->assign("data",$res);//分配数据
			$this->display();
		}
	}
}
 ?>