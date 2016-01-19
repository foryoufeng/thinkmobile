<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/11
 * Time: 15:50
 */

namespace Common\Model;
use Think\Model;
use Think\Page;

class CommonModel extends Model
{
    const MSUCCESS=0;//操作成功
    const MFAIL=1;//操作失败
    const MMSG=2;//操作返回的消息
    const LIMIT=3;//分页条数
    /**
     * 添加数据
     * @return mixed
     */
    public function addData($info){
        $data=$this->create($info);
        if($data){
            $id=$this->add();
            if($id){
                return CommonModel::MSUCCESS;//添加成功
            }else{
                return CommonModel::MFAIL;//添加失败
            }
        }else{
            return $this->getError();//添加错误的原因
        }
    }
    public function getOne($where){
        return $this->where($where)->find();
    }
    public function getAll(){
        $page=I('p',1);
        if(method_exists($this,'getWhere')){
            $where=  $this->getWhere();
        }
        $list =$this->where($where)->order('id DESC')->page($page.','.CommonModel::LIMIT)->select();
        return $list;
    }
    public function getPage(){
        if(method_exists($this,'getWhere')){
            $where=  $this->getWhere();
        }
        $count      = $this->where($where)->count();// 查询满足要求的总记录数
        $page=new Page($count,CommonModel::LIMIT);
        $show=$page->show();
        return $show;
    }
    /**
     * 编辑数据
     * @return int|string MSUCCESS表示成功  MFAIL表示失败  getError返回错误信息
     */
    public function editData($info){
        $data = $this->create($info);
        if($data){
            if($this->save()!== false){
                return CommonModel::MSUCCESS;//更新成功
            }else{
                return CommonModel::MFAIL;//更新失败
            }
        }else{
            return  $this->getError();
        }
    }

    /**
     * 根据条件获取所有数据
     * @param $where
     * @return mixed
     */
    public function getCount($where){
        $count      = $this->where($where)->count();// 查询满足要求的总记录数
        return $count;
    }
    protected function msg($data){
        ajax($data);
    }
    protected function adds($info){
        $data=$this->create($info);
        if($data){
            $id=$this->add();
            return $id;
        }else{
            return 0;//添加错误的原因
        }
    }
}