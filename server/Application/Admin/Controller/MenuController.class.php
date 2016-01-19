<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/16
 * Time: 16:42
 */

namespace Admin\Controller;
use Common\Model\CommonModel;
use Common\Util\WeiUtil;

class MenuController extends CommonController
{
    function _initialize(){
        $this->model=D('Menu');
    }
    /**
     * 添加数据
     */
    public function add(){
        $list=$this->model->getList();
        if(IS_POST){
            $parent_id=I("parent_id");
            if(count($list)>2 &&!$parent_id){
                $this->error("已经有3个主菜单了");
            }
            $res=$this->model->addData();
            if($res==CommonModel::MSUCCESS){
                $this->success("添加成功");
            }else{
                $this->error("添加失败");
            }
        }else{
            $this->assign('list',$list);
            $this->display();
        }

    }

    /**
     * 修改
     */
     public  function save(){
         $id=I('id',0);
         if(IS_POST){
             $res=$this->model->editData();
             if($res==CommonModel::MSUCCESS){
                 $this->success("修改成功");
             }else{
                 $this->error($this->model->getError()."   修改失败");
             }
         }else{
             $list=$this->model->getList();
             $where['id']=$id;
             $data=$this->model->getOne($where);
             $this->assign('list',$list);
             $this->assign('data',$data);
             $this->display();
         }
     }
    public function generate(){
        $menu=$this->model->generate();
        $wei=new WeiUtil();
        $res=$wei->create_menu($menu);
       // return;
        if($res['errcode']==0){
            $this->success("修改成功");
        }else{
            $this->error($res['errcode']."-----".$res['errmsg']."   修改失败");
        }
    }
}