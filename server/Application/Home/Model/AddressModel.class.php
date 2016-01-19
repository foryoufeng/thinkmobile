<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/11
 * Time: 15:47
 */

namespace Home\Model;
use Common\Model\CommonModel;

class AddressModel extends CommonModel
{
    private  $success=array('code'=>0,'msg'=>'success');
    private  $fail=array('code'=>1,'msg'=>'fail');
    protected $tableName='User_address';
    protected $_validate = array(
       // array('user_id','require','用户id必须！'), //默认情况下用正则进行验证
        array('consignee','require','姓名必须！'), // 在新增的时候验证name字段是否唯一
        array('mobile','require','手机号必须！'), // 在新增的时候验证name字段是否唯一
        array('address','require','地址必须！'), // 在新增的时候验证name字段是否唯一
        array('province','require','所属省市必须！'),//在新增的时候验证name字段是否唯一
        array('city','require','所属省市必须！'),//在新增的时候验证name字段是否唯一
       // array('address','require','所属地区必须！'),//在新增的时候验证name字段是否唯一
    );
    public function getAll(){
        $where['user_id']=session('user.user_id');
        $province=array();
        $city=array();
        $district=array();
        $list =$this->field('address_id,consignee,province,city,district,address,mobile,sign_building')->where($where)->order('address_id ASC')->select();
        for($i=0;$i<count($list);$i++){
            $where['region_id']=$list[$i]['province'];
            $province[$i]=D('Region')->where($where)->select();
            $list[$i]['province']=$province[$i][0]['region_name'];
            $where1['region_id']=$list[$i]['city'];
            $city[$i]=D('Region')->where($where1)->select();
            $list[$i]['city']=$city[$i][0]['region_name'];
            $where2['region_id']=$list[$i]['district'];
            $district[$i]=D('Region')->where($where2)->select();
            $list[$i]['district']=$district[$i][0]['region_name'];
            $list[$i]['addr']=$list[$i]['province'].$list[$i]['city'].$list[$i]['district'].$list[$i]['address'];
            unset($list[$i]['province']);
            unset($list[$i]['city']);
            unset($list[$i]['district']);
            unset($list[$i]['address']);

        }
        //print_r($province);die;
        return $list;
    }
    public function  getOne(){
        $data=array();
        $where=array(
            'user_id'=>session('user.user_id'),
            'sign_building'=>1,
        );
        $list=$this->field('address_id,province,city,district,consignee,address,mobile')->where($where)->find();
        if($list){
            $where['region_id']=$list['province'];
            $province=M('Region')->where($where)->find();
            $where['region_id']=$list['city'];
            $city=M('Region')->where($where)->find();
            $where['region_id']=$list['district'];
            $district=M('Region')->where($where)->find();
            $data['address']=$province['region_name'].$city['region_name'].$district['region_name'].$list['address'];
            $data['consignee']=$list['consignee'];
            $data['mobile']=$list['mobile'];
            $data['address_id']=$list['address_id'];
        }
        return $data;
    }
    public function city(){
        $region_id=I('region_id',1);
        if($region_id==1){
            //返回省市
            $where['parent_id']=array('eq',1);
            $data=D('Region')->field('region_id,region_name')->where($where)->select();
            return $data;
        }else{
            //返回区县
            $where1['parent_id']=I('region_id');
            $data1=D('Region')->field('region_id,region_name')->where($where1)->select();
            return $data1;
        }
    }
    /**
     * 添加数据
     * @return mixed
     */
    public function addData(){
        //添加收货人地址信息
        $where2['user_id']=session('user.user_id');
        $res=$this->where($where2)->count();
        if(!$res){
            //没有收货地址
            $data['user_id']=session('user.user_id');
            $data['consignee']=I('consignee');//收货人姓名
            $data['mobile']=I('mobile');//收货人电话
            $data['country']=1;//默认国家为中国
            $data['province']=I('province');//省
            $data['city']=I('city');//市
            $data['district']=I('district');//地区
            $data['address']=I('address');//详细地址
            $data['sign_building']=1;//第一次填写地址为默认收货地址
            //parent::addData($data);
            $res=$this->add($data);
            if($res){
                return $this->success;
            }else{
                return $this->fail;
            }

        }else if($res<5){
            //已经有收货地址，但没有超过五条收货地址信息
            $data['user_id']=session('user.user_id');
            $data['consignee']=I('consignee');//收货人姓名
            $data['mobile']=I('mobile');//收货人电话
            $data['country']=1;//默认国家为中国
            $data['province']=I('province');//省
            $data['city']=I('city');//市
            $data['district']=I('district');//地区
            $data['address']=I('address');//详细地址
            $data['sign_building']=0;//非第一次填写收货地址不作为默认地址
            //parent::addData($data);
            $res=$this->add($data);
            if($res){
                return $this->success;
            }else{
                return $this->fail;
            }
        }else{
            return $this->fail;
        }

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
    /**
     * 用户删除收获地址
     */
    public function del(){
        $address_id=I('address_id');//必须参数，获取需要删除的地址
        $user_id=session('user.user_id');
        $where['user_id']=$user_id;
        $where['address_id']=$address_id;
        $list=$this->where($where)->delete();
        if($list){
            return $this->success;
        }else{
            return $this->fail;
        }
    }
}