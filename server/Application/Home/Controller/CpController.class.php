<?php
namespace Home\Controller;
use Think\Controller;
class CpController extends Controller {
//	填写产品编号获取价格
    public function index(){
    	$this->display();
    }

//	AJAX获取金额
	public function ajax(){
		if(IS_AJAX){
			$where['bh']=I('bh');
			$res=S('data');
			if($res &&$res['bh']==$where['bh']){
				$res=S('data');
			}else{
				$res = D('Sp')->where($where)->find();
				S('data',$res,36000,'File',array('length'=>10,'temp'=>RUNTIME_PATH.'Temp/'));
		}
			$money=isset($res['monay'])?$res['monay']:0;
			$this->success($money);
		}
	}
}