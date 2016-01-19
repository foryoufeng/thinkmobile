<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/16
 * Time: 11:48
 */

namespace Home\Model;


use Common\Model\CommonModel;

class JgModel extends CommonModel
{
    public function getWhere(){
        $where=array(
            'user_id'=>session('user.id')
        );
        return $where;
    }

}