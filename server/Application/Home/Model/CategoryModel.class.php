<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/22
 * Time: 10:14
 */

namespace Home\Model;


use Common\Model\CommonModel;

class CategoryModel extends CommonModel
{
    public function getAll(){
        $page=I('p',1);
        $list =$this->field('cat_id,cat_name,cat_desc')->order('cat_id DESC')->page($page.','.CommonModel::LIMIT)->select();
        return $list;
    }
}