<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/22
 * Time: 10:14
 */

namespace Home\Model;


use Common\Model\CommonModel;

class CommentModel extends CommonModel
{
    private  $success=array('code'=>0,'msg'=>'success');
    private  $fail=array('code'=>1,'msg'=>'fail');
    private  $norank=array('code'=>2,'msg'=>'请选择评价等级');
    private  $nocontnt=array('code'=>3,'msg'=>'请填写评价内容');
    private  $nogoods=array('code'=>4,'msg'=>'no goods id');
    //protected $_validate = array(
        //array('user_id','require','�û�id���룡'), //Ĭ������������������֤
        //array('id_value','require','��Ʒid���룡'), // ��������ʱ����֤id_value�ֶ��Ƿ�Ψһ
        //array('comment_rank','require','���۵ȼ����룡'), // ��������ʱ����֤comment_rank�ֶ��Ƿ�Ψһ
        //array('content','require','�������ݱ��룡'), // ��������ʱ����֤content�ֶ��Ƿ�Ψһ
    //);

    public function getAll(){
        $user_id=session('user.user_id');
        $where['user_id']=$user_id;
        $list=$this->field('comment_id,content,id_value,comment_rank,add_time')->where($where)->select();
        for($i=0;$i<count($list);$i++){
            $list[$i]['add_time']=date('Y-m-d',$list[$i]['add_time']);
        }
        return $list;
    }



    public function addData(){
        $goods_id=I('goods_id');//�ش�����,��ȡ�ĸ���Ʒ�µ�����
        $content=I('content');//��������
        $comment_rank=I('comment_rank');//�������ǵȼ�
        if($goods_id==''){
            return $this->nogoods;
        }else if($content==''){
            return $this->nocontnt;
        }else if($comment_rank==''){
            return $this->norank;
        }
        $data['user_id']=session('user.user_id');
        $data['id_value']=$goods_id;
        $data['content']=$content;
        $data['comment_rank']=$comment_rank;
        $data['add_time']=time();
        $where['user_id']=session('user.user_id');
        $user_name=D('User')->field('user_name')->where($where)->find();
        $data['user_name']=$user_name['user_name'];
        //return parent::addData($data);
        $res=$this->add($data);
        if($res){
            return $this->success;
        }else{
            return $this->fail;
        }
    }
}