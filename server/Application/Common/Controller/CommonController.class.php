<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/15
 * Time: 9:45
 */

namespace Common\Controller;
use Common\Util\WeiUtil;
use Think\Controller;

class CommonController extends Controller
{
    protected $wei;
    protected $model;
    function _initialize(){
        //session('user',array('id'=>2));
    }
    /**
     * 根据条件分页显示所有数据
     */
    public function index(){
        $search=I('search',null);
        $list= $this->model->getAll();
        $show=$this->model->getPage();
        if($list){
            $data['list'] =$list;
            $this->ajaxReturn($data);
        }else{
            $this->ajaxReturn(0);
        }
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
    protected function msg($data){
        if($data){
            ajax($data);
        }else{
            ajax(0);
        }
    }
    public function isLogin(){
            $flag=true;
            if(session('user.user_id')<1){
                $data['code']=5;
                $data['msg']='no login';
                $this->msg($data);
            }
            return $flag;
    }
}