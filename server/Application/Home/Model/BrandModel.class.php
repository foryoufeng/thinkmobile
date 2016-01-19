<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/22
 * Time: 10:14
 */

namespace Home\Model;


use Common\Model\CommonModel;

class BrandModel extends CommonModel
{
    public function getAll(){
        $page=I('p',1);
        $type=I('type',1);
        $user_id=session('user.user_id');
        if($type==1){//������Ʒ
            //判断用户是否登录
            $where['sort_order']=50;
            if($user_id){
                $list=$this->alias('b')->field('b.brand_id,b.brand_name,b.brand_logo,b.brand_desc,b.site_url,f.brand_id as follow')->join("left join __FOCUS__ f on f.brand_id=b.brand_id and f.user_id=$user_id")->where($where)->order('b.brand_id DESC')->page($page.','.CommonModel::LIMIT)->select();
                for($j=0;$j<count($list);$j++){
                    if($list[$j]['follow']){
                        $list[$j]['follow']=1;
                    }else{
                        $list[$j]['follow']=0;
                    }
                }
            }else{
                $list =$this->where($where)->order('brand_id DESC,sort_order DESC')->page($page.','.CommonModel::LIMIT)->select();
                //用户没有登录fllow字段赋值
                for($i=0;$i<count($list);$i++){
                    $list[$i]['follow']=0;
                }
            }
           foreach($list as $k=>$v){
               $where2['brand_id']=$v['brand_id'];
               $where2['goods_thumb']=array('neq','');
               $where1['brand_id']=$v['brand_id'];
               $list[$k]['imgs']=D("Goods")->field("goods_id,goods_thumb,keywords")->where($where2)->order('goods_id DESC')->limit(3)->select();
               $list[$k]['num']=M('Goods')->where("brand_id=$v[brand_id]")->count();
               $list[$k]['fans']=D('Focus')->where($where1)->count();
           }
        }else{//������Ʒ
            //判断用户是否登录
            $where3['sort_order']=60;
            if($user_id){
                $list=$this->alias('b')->field('b.brand_id,b.brand_name,b.brand_logo,b.brand_desc,b.site_url,f.brand_id as follow')->join("left join __FOCUS__ f on f.brand_id=b.brand_id and f.user_id=$user_id")->where($where3)->order('b.brand_id DESC')->page($page.','.CommonModel::LIMIT)->select();
                for($j=0;$j<count($list);$j++){
                    if($list[$j]['follow']){
                        $list[$j]['follow']=1;
                    }else{
                        $list[$j]['follow']=0;
                    }
                }
            }else{
                $list =$this->where($where3)->order('brand_id DESC,sort_order DESC')->page($page.','.CommonModel::LIMIT)->select();
                //用户没有登录fllow字段赋值
                for($i=0;$i<count($list);$i++){
                    $list[$i]['follow']=0;
                }
            }
            foreach($list as $k=>$v){
                $where4['brand_id']=$v['brand_id'];
                $where4['goods_thumb']=array('neq','');
                $where1['brand_id']=$v['brand_id'];
                $list[$k]['imgs']=D("Goods")->field("goods_id,goods_thumb,keywords")->where($where4)->order('goods_id DESC')->limit(3)->select();
                $list[$k]['num']=M('Goods')->where("brand_id=$v[brand_id]")->count();
                $list[$k]['fans']=D('Focus')->where($where1)->count();
            }
        }
        return $list;
    }
}