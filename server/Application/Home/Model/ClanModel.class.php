<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/11
 * Time: 15:47
 */

namespace Home\Model;
use Common\Model\CommonModel;

class ClanModel extends CommonModel
{
    protected $_validate = array(
        array('user_id','require','用户id必须！'), //默认情况下用正则进行验证
        array('name','require','姓名必须！'), // 在新增的时候验证name字段是否唯一
        array('phone','require','手机号必须！'), // 在新增的时候验证name字段是否唯一
        array('address','require','地址必须！'), // 在新增的时候验证name字段是否唯一
    );
    public function getOne($userid){
        $where['user_id']=$userid;
        $res=$this->where($where)->find();
        return $res;
    }
    /**
     * 添加数据
     * @return mixed
     */
    public function addData(){
        $data['user_id']=session('user.id');
        $data['name']=I('name');
        $data['phone']=I('phone');
        $data['address']=I('address');
        return parent::addData($data);
    }
    /**
     * 编辑数据
     * @return int|string MSUCCESS表示成功  MFAIL表示失败  getError返回错误信息
     */
    public function editData($id){
        $data['user_id']=session('user.id');
        $data['name']=I('name');
        $data['phone']=I('phone');
        $data['address']=I('address');
        $data['id']=$id;
        return parent::editData($data);
    }
}