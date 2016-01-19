<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/16
 * Time: 11:48
 */

namespace Home\Model;


use Common\Model\CommonModel;

class FocusModel extends CommonModel
{
    private  $success=array('code'=>0,'msg'=>'success');
    private  $fail=array('code'=>1,'msg'=>'fail');
    private  $nologin=array('code'=>2,'msg'=>'please login first');
    private  $nobrand=array('code'=>3,'msg'=>'no brand_id');
    private  $hbrand=array('code'=>4,'msg'=>'brand already exists');
    private $user_id;
    protected function _initialize() {
        $this->user_id=session('user.user_id');
    }
//    public function getWhere(){
//        $where=array(
//            'user_id'=>session('user.user_id')
//        );
//        return $where;
//    }
    //��ע�б�,�û���¼���ѹ�עƷ����ʾ�ѹ�ע
    public function tabulation(){
//        if(!$this->user_id){
//            $list['unlogin']=0;
//            return $list;
//        }
        $page=I('p',1);
        //$type=I('type',1);
        //��ʾ���û���ע������Ʒ��
        $list=$this->alias('f')->field("b.brand_name,b.brand_logo,b.brand_desc,b.site_url,b.brand_id")->where("f.user_id=$this->user_id")->join('__BRAND__ b on f.brand_id=b.brand_id')->order('f.brand_id DESC')->select();
        //�������ʦ��˿��
        $len=count($list);
        for($i=0;$i<$len;$i++){
            $where['brand_id']=$list[$i]['brand_id'];
            $list[$i]['fans']=D('Focus')->where($where)->count();
        }
        if(!$list){
            return $this->fail;//���û�û�й�עʱ����1
        }
        //�û���¼���ڹ�ע�б����ҳ�棬���û���עƷ����ʾ�ѹ�ע
//        if($type==1){
//            $nwhere['b.sort_order']=50;
//            $list=$this->alias('f')->field('b.brand_id,b.brand_name,b.brand_logo,b.brand_desc,b.site_url,f.brand_id as follow')->join("right join __BRAND__ b on f.brand_id=b.brand_id and f.user_id=$this->user_id")->where($nwhere)->order('b.brand_id DESC')->page($page.','.CommonModel::LIMIT)->select();
//            foreach($list as $k=>$v){
//                $where['brand_id']=$v['brand_id'];
//                $where['goods_thumb']=array('neq','');
//                $where1['brand_id']=$v['brand_id'];
//                $list[$k]['imgs']=D("Goods")->field("goods_id,goods_thumb,keywords")->where($where)->order('goods_id DESC')->limit(3)->select();
//                $list[$k]['num']=M('Goods')->where("brand_id=$v[brand_id]")->count();
//                $list[$k]['fans']=D('Focus')->where($where1)->count();
//            }
//        }else{
//            $nwhere['b.sort_order']=60;
//            $list=$this->alias('f')->field('b.brand_id,b.brand_name,b.brand_logo,b.brand_desc,b.site_url,f.brand_id as follow')->join("right join __BRAND__ b on f.brand_id=b.brand_id and f.user_id=$this->user_id")->where($nwhere)->order('b.brand_id DESC')->page($page.','.CommonModel::LIMIT)->select();
//            foreach($list as $k=>$v){
//                $where['brand_id']=$v['brand_id'];
//                $where['goods_thumb']=array('neq','');
//                $where1['brand_id']=$v['brand_id'];
//                $list[$k]['imgs']=D("Goods")->field("goods_id,goods_thumb,keywords")->where($where)->order('goods_id DESC')->limit(3)->select();
//                $list[$k]['num']=M('Goods')->where("brand_id=$v[brand_id]")->count();
//                $list[$k]['fans']=D('Focus')->where($where1)->count();
//            }
//        }
        return $list;
    }
    //��ӹ�ע
    public function addData(){
        $brand_id=I('get.brand_id');
        $user_id=$this->user_id;
        if(!$user_id){
            return $this->nologin;
        }else{
            $where['brand_id']=$brand_id;
            $where['user_id']=$user_id;
            $res=$this->where($where)->find();
            if($res){
                return $this->hbrand;
            }
            if($brand_id==''){
                return $this->nobrand;
            }
            $time = time();
            $data['user_id']=$this->user_id;
            $data['brand_id']=$brand_id;
            $data['time']=$time;
            $list =parent::addData($data);
            if($list==CommonModel::MSUCCESS){
                return $this->success;
            }else{
                return $this->fail;
            }
        }
        //return $list;
    }
    //ȡ����ע
    public function delete(){
        $brand_id=I('get.brand_id');
        $where=array(
            'user_id'=>$this->user_id,
            'brand_id'=>$brand_id,
        );
        $list=M('Focus')->where($where)->delete();
        if($list==CommonModel::MFAIL){
            return  $this->success;;
        }else{
            return $this->fail;
        }
        //return $list;
    }
}