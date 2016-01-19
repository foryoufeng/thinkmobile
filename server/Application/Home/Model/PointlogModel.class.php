<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/11
 * Time: 15:47
 */

namespace Home\Model;
use Common\Model\CommonModel;

class PointlogModel extends CommonModel
{
    const ADD=1;//购买商品增加的积分
    const DEC=2;//购买商品使用的积分
    protected $_validate = array(
        array('user_id','require','用户id必须！'), //默认情况下用正则进行验证
        array('point','require','积分必须！'), // 在新增的时候验证name字段是否唯一
        array('type',array(1,2),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
    );
    protected $_auto = array (
        array('time','time',3,'function'), // 对time字段在新增和更新的时候写入当前时间戳
    );

    /**
     * 记录用户的积分情况
     * @param $user_id 用户的id
     * @param $point   积分数
     * @param $info    操作信息
     * @param $type    类型  添加或者减少
     * @return mixed   返回添加状态
     */
    public function log($user_id,$point,$info,$type=SELF::ADD){
        D('User')->editPoint($user_id,$point,$type);
        $data=array(
            'user_id'=>$user_id,
            'point'=>$point,
            'info'=>$info,
            'type'=>$type
        );
        return $this->addData($data);
    }
}