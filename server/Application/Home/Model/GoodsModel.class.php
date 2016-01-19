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
    private  $fail=array('code'=>1,'msg'=>'no order');
    private $user_id;
    protected function _initialize() {
        $this->user_id=session('user.user_id');
    }
    public function getAll(){
        $page=I('p',1);
        $type=I('get.type',1);
        $goods_id=I('get.goods_id',0);
        //$goods_id=38;
        if($type==1){//��Ʒ��������Ʒ����
            $list =$this->field('goods_id,goods_name,shop_price,cat_id,brand_id,goods_desc,goods_type,goods_number,goods_thumb')->where("goods_id=$goods_id")->find();

                $where['goods_id']=$list['goods_id'];
                $where['img_url']=array('neq','');
                $where1['brand_id']=$list['brand_id'];
                $goods_type=$list['goods_type'];
                $where2['cat_id']=$goods_type;
                $list['imgs']=D("GoodsGallery")->field("img_url")->where($where)->select();
                $list['brand']=D("Brand")->field("brand_name,brand_logo")->where($where1)->select();
                $list['fans']=D('Focus')->where($where1)->count();
                $allname = D('Attribute')->field('attr_id,attr_name')->where($where2)->order('sort_order ASC')->select();
                $len = count($allname);

                $arr = array();
                for($i=0;$i<$len;$i++){
                    $where3['attr_id']=$allname[$i]['attr_id'];
                    $where4['goods_id']=$goods_id;
                    $v=D('GoodsAttr')->field('attr_value')->where($where3)->where($where4)->find();
                    $arr[$i]['name']=$allname[$i]['attr_name'];
                    $arr[$i]['value']=$v['attr_value'];
                }
                $list['parameter']=$arr;
                $list['goods_desc']=mb_substr(strip_tags($list['goods_desc']),0,10,'utf-8');
                //���ؼ��빺�ﳵ���������,��������
                $model = M();
                $sql="select g.attr_id,a.attr_name from ibh_attribute a,ibh_goods_attr g WHERE a.attr_id=g.attr_id and g.goods_id=$goods_id and a.attr_type>0 GROUP BY g.attr_id";
                $tanchu = $model->query($sql);
                $result=array();
                $len1=count($tanchu);
                for($i=0;$i<$len1;$i++){
                    $attr_id=$tanchu[$i]['attr_id'];
                    $sql1="SELECT attr_value,goods_attr_id as id from ibh_goods_attr WHERE attr_id=$attr_id and goods_id=$goods_id";
                    $result[$i]=$model->query($sql1);
                }
                $len2=count($result);
                for($j=0;$j<$len2;$j++){
                    $list['type'][$j]['name']=$tanchu[$j]['attr_name'];
                    $list['type'][$j]['value']=$result[$j];
                }
        }else{//��Ʒ����
            $list =$this->field('goods_id,goods_name,shop_price,cat_id,brand_id,goods_desc,goods_thumb')->where("goods_id=$goods_id")->find();
                $where['goods_id']=$list['goods_id'];
                $where['img_url']=array('neq','');
                $where1['brand_id']=$list['brand_id'];
                $where2['id_value']=$list['goods_id'];
                $list['imgs']=D("GoodsGallery")->field("img_url")->where($where)->select();
                $list['brand']=D("Brand")->field("brand_name,brand_logo")->where($where1)->find();
                //$list['discuss'] =D("Comment")->field("user_name,content,comment_rank")->where($where2)->select();
                $res=D("Comment")->alias('c')->field("c.user_name,c.content,c.comment_rank,c.add_time,c.user_id,u.alias")->where($where2)->join('__USERS__ u on c.user_name=u.user_name')->order('c.comment_id DESC')->page($page.','.CommonModel::LIMIT)->select();
                $len=count($res);
                //��Ʒ��������Ʒ��Ϣ
                $model=M();
                $rut=array();
                $rut1=array();
                for($i=0;$i<$len;$i++){
                    $user_id=$res[$i]['user_id'];
                    $res[$i]['add_time']=date('Y-m-d',$res[$i]['add_time']);
                    $sql3="select g.goods_attr_id from ibh_order_goods as g,ibh_order_info as i where g.order_id=i.order_id and g.goods_id=$goods_id and i.user_id=$user_id  order by g.rec_id desc limit 1";
                    $rut[]=$model->query($sql3);
                }
//                if(!$rut){
//                    return $this->fail;
//                }
                for($k=0;$k<count($rut);$k++){
                    $str=$rut[$k][0]['goods_attr_id'];
                    $sql4="select a.attr_name as name,b.attr_value as value from ibh_attribute as a,ibh_goods_attr as b where a.attr_id=b.attr_id and b.goods_id=$goods_id and b.goods_attr_id in($str) order by b.goods_attr_id desc";
                    $rut1[]=$model->query($sql4);
                }
                $character=array();
                for($v=0;$v<count($rut1);$v++){
                    for($m=0;$m<count($rut1[$v]);$m++){
                        $character[$v].=$rut1[$v][$m]['name'].':'.$rut1[$v][$m]['value'].' ';
                    }
                }
                for($n=0;$n<count($character);$n++){
                    $res[$n]['goods_info']=$character[$n];
                }
                if($res){
                    $list['discuss']=$res;
                }else{
                    $list['discuss']=0;
                }
                $list['goods_desc']=mb_substr(strip_tags($list['goods_desc']),0,10,'utf-8');
                $list['fans']=D('Focus')->where($where1)->count();


        }

        return $list;
    }
}