<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/16
 * Time: 17:37
 */

namespace Admin\Model;
use Common\Model\CommonModel;

class MenuModel extends CommonModel
{
    /**
     * 获取根菜单
     * @return mixed
     */
    public function getList(){
         $where['parent_id']=0;
         return $this->where($where)->select();
     }

    /**
     * 获取所有菜单
     * @return 显示数据
     */
    public function getAll(){
        $list=array();
        $where['parent_id']=0;
        $data =$this->where($where)->order('id ASC')->select();
        $i=0;
        foreach($data as $v){
            $list[$i]=$v;
            $where['parent_id']=$v['id'];
            $childs=$this->where($where)->order('id ASC')->select();
            foreach($childs as $val){
                $i++;
                $list[$i]=$val;
                $list[$i]['name']='-----'.$val['name'];
            }
            $i++;
        }
        return $list;
    }

    /**
     * @return array
     *
     */
    public function generate(){
        $res=array();
        $list=array();
        $where['parent_id']=0;
        $data =$this->where($where)->order('id ASC')->select();
        $i=0;
        foreach($data as $v){
            $list[$i]=array();
            $where['parent_id']=$v['id'];
            $childs=$this->where($where)->order('id ASC')->select();
            $list[$i]['name']=$v['name'];
            foreach($childs as $k=>$val){
                if($val['type']==1){
                    $list[$i]['sub_button'][$k]['type']='view';
                    $list[$i]['sub_button'][$k]['url']=$val['url'];
                }elseif($val['type']==2){
                    $list[$i]['sub_button'][$k]['type']='click';
                    $list[$i]['sub_button'][$k]['key']=$val['url'];
                   //$list[$i]['sub_button'][$k]['type']='view';
                   // $list[$i]['sub_button'][$k]['url']=$val['url'];
                }
                $list[$i]['sub_button'][$k]['name']=$val['name'];
            }
            $i++;
        }
        $res['button']=$list;
        return $res;
    }
}