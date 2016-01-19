<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/22
 * Time: 10:14
 */

namespace Home\Model;


use Common\Model\CommonModel;

class GoodsModel extends CommonModel
{
    public function getAll(){
        $page=I('p',1);
        $goods=I('goods',3);
        //$goods_id=I('get.goods_id');
        $goods_id=17;
        if($goods==1 || $goods==2){//��Ʒ��������Ʒ����
            $list =$this->where("goods_id=$goods_id")->select();
            foreach($list as $k=>$v){
                $where['goods_id']=$v['goods_id'];
                $where['img_url']=array('neq','');
                $list[$k]['imgs']=D("GoodsGallery")->field("img_url")->where($where)->select();
                $list[$k]['brand_name']=D("Brand")->field("brand_name")->where("brand_id=$v[brand_id]")->select();
                //$list[$k]['goods_thumb']=$v['original_img'];
            }
            print_r($list);die;
        }else{//��Ʒ����
            $list =$this->where("goods_id=$goods_id")->select();
            foreach($list as $k=>$v){
                $where['goods_id']=$v['goods_id'];
                $where['img_url']=array('neq','');
                $list[$k]['imgs']=D("GoodsGallery")->field("img_url")->where($where)->select();
                $list[$k]['brand_name']=D("Brand")->field("brand_name")->where("brand_id=$v[brand_id]")->select();
                $list[$k]['discuss'] =D("Comment")->where("id_value=$v[goods_id]")->select();
            }
        }

        return $list;
    }
}