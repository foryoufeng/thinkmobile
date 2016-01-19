<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/11
 * Time: 15:47
 */

namespace Home\Model;
use Common\Model\CommonModel;

class UserModel extends CommonModel
{
    protected $tableName='Users';
     public function is_add($openid){
         $where['openid']=$openid;
         $res=$this->where($where)->find();
         return $res;
     }
     public function login(){
       //  $where['name']=I('post.name','0');
        $where['email']=I('name','0');
        // $where['password']=md5(I('post.password','0'));
         $where['password']=md5(I('password','0'));
         $res=$this->getOne($where);
         if($res){
             $res['auth']=$this->getAuth($res);
         }
         return $res;
     }
    public function autoLogin(){
        $where['user_id']=I('id',0);
        $auth=I('auth',0);
        $res=session('user.auth');
        if($res===$auth){
             return 1;
          }else{
            return 0;
        }
    }
     public function getAll(){
         $user=session('user');
         $data=null;
         if($user){
             $data['portrait']=$user['alias'];//头像
             $data['user_name']=$user['user_name'];//头像
             $order=D('Order');
             $where=$order->getWhere(OrderModel::NOPAY);
             $data['wpay']=$order->getCount($where);
             $where=$order->getWhere(OrderModel::WSEND);
             $data['wsend']=$order->getCount($where);
             $where=$order->getWhere(OrderModel::WGET);
             $data['wget']=$order->getCount($where);
             $where=$order->getWhere(OrderModel::WCOMMENT);
             $data['wcomment']=$order->getCount($where);
             $cart=D('Cart');
             $where['user_id']=$user['user_id'];
             $data['cart']=$cart->getCount($where);
         }
         return $data;
     }
    private function  getAuth($res){
        $data=md5($res['user_id'].$res['email'].$res['password']);
        return $data;
    }

    //用户注册接口

}