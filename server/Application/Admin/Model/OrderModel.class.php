<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/18
 * Time: 10:27
 */

namespace Admin\Model;
use Common\Model\CommonModel;

class OrderModel extends CommonModel
{
    public function getWhere(){
        $search=I('search',null);
        if($search){
            $map['name|order_id|phone'] =array('like',"%$search%");
        }
        return $map;
    }
}