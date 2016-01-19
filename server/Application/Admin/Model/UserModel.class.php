<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/15
 * Time: 15:19
 */

namespace Admin\Model;
use Common\Model\CommonModel;
use Think\Page;

class UserModel extends CommonModel
{
       public function getAll(){
           if(I('username')){
               $where['phone']=I('username');
           }else{
               $where='u.id>0';
           }
           $page=I('p',1);
           $list =$this->alias('u')->field("u.*,a.name,a.address,a.phone")->join('LEFT JOIN __ADDRESS__ a on u.id=a.user_id')->where($where)->order('u.id DESC')->page($page.','.CommonModel::LIMIT)->select();
           return $list;
       }
      public function getPage(){
          if(I('username')){
              $where['phone']=I('username');
          }else{
              $where='id>0';
          }
          $count= $this->where($where)->count();// 查询满足要求的总记录数
          $page=new Page($count,CommonModel::LIMIT);
          $show=$page->show();
          return $show;
      }
    public function getWhere(){

    }
}