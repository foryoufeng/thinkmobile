<?php
namespace Home\Controller;
use Common\Controller\CommonController;
use Common\Util\WeiUtil;
use Common\Util\Weixin\JsApiPay;
use Common\Util\Weixin\WxPayApi;
use Common\Util\Weixin\WxPayConfig;
use Common\Util\Weixin\WxPayUnifiedOrder;
use Home\Model\CommentModel;

class IndexController extends CommonController {
	function _initialize(){
		parent::_initialize();
		//$this->model=D('Goods');
	}
	function get_flash_xml()
	{
		$flashdb = array();

		$url = dirname(dirname($_SERVER['SCRIPT_FILENAME'])).'/data';
		if (file_exists($url.'/flash_data.xml'))
		{

			// 兼容v2.7.0及以前版本
			if (!preg_match_all('/item_url="([^"]+)"\slink="([^"]+)"\stext="([^"]*)"\ssort="([^"]*)"/', file_get_contents($url. '/flash_data.xml'), $t, PREG_SET_ORDER))
			{
				preg_match_all('/item_url="([^"]+)"\slink="([^"]+)"\stext="([^"]*)"/', file_get_contents($url . '/flash_data.xml'), $t, PREG_SET_ORDER);
			}
			if (!empty($t))
			{
				foreach ($t as $key => $val)
				{
					$val[4] = isset($val[4]) ? $val[4] : 0;
					$flashdb[] = array('src'=>$val[1],'url'=>$val[2]);
				}
			}
		}
		return $flashdb;
	}
	//	填写产品编号获取价格
	public function index() {
		$page=I('p',1);
		//首页幻灯片
		$playerdb = $this->get_flash_xml();
		foreach ($playerdb as $key => $val)
		{
			if (strpos($val['src'], 'http') === false)
			{
				$playerdb[$key]['src'] = $uri . $val['src'];
			}
		}
		//首页最新展示产品
		$list['goods'] =D('Goods')->field('goods_id,goods_name,goods_thumb,goods_desc,add_time')->where("goods_thumb!=''")->order('goods_id DESC')->page($page.','.CommentModel::LIMIT)->select();
		foreach($list['goods'] as $k=>$v){
			$list['goods'][$k]['add_time']=date('Y-m-d',$v['add_time']);
			$list['goods'][$k]['goods_desc']=mb_substr(strip_tags($v['goods_desc']),0,10,'utf-8');
		}
		$list['imgs'] =$playerdb;
		ajax($list);
	}

	//	AJAX获取金额
	public function ajax() {
		if (IS_AJAX) {
			$where = array('bh' => $_POST['bh'], );
			$res = D('Sp') -> where($where) -> find();
			$this -> ajaxReturn($res['monay']);
		}
	}
    public function pay(){
		$user=session('user');
		$tools=new JsApiPay();
		$openId=$tools->GetOpenid();
		session('openId',$openId);
		if($user){
			$this->assign('jifen',$user['jifen']);
		}
		$this -> display();
	}
	private function receiveEvent($object){
		$content = "";
		switch ($object->Event) {
			case "subscribe" :
				$content[] = array("Title" => "欢迎关注方倍工作室", "Description" => "", "PicUrl" => "http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" => "http://m.cnblogs.com/?u=txw1958");
				break;
			case "CLICK" :
				switch ($object->EventKey) {
					case "图文" :
						$content[] = array("Title" => "OpenID", "Description" => "你的OpenID为：" . $object -> FromUserName, "PicUrl" => "", "Url" => "http://m.cnblogs.com/?u=txw1958&openid=" . $object -> FromUserName);
						break;
				}
				break;
		}
		if (is_array($content)) {
			$result = $this -> transmitNews($object, $content);
		} else {
			$result = $this -> transmitText($object, $content);
		}
		return $result;
	}

}
