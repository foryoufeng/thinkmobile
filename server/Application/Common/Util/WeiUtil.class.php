<?php
namespace Common\Util;
use Common\Util\Weixin\JsApiPay;
use Common\Util\Weixin\WxPayApi;
use Common\Util\Weixin\WxPayConfig;
use Common\Util\Weixin\WxPayUnifiedOrder;

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/12/11
 * Time: 14:20
 */
class WeiUtil{
    private $appid;
    private $appsecret;
    function  __construct(){
        $this->appid=C('APPID');
        $this->appsecret=C('APPSECRET');
    }
    public function get_token(){
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->appsecret";
        $jsoninfo = exe_url($url);
        return $jsoninfo["access_token"];
    }
    public function create_menu($menu){
        $token=$this->get_token();
        $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$token";
        $data=json_encode($menu,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        //echo $data;return;
        return exe_url($url,$data);
    }
    public function get_access_token($code){
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->appid&secret=$this->appsecret&code=$code&grant_type=authorization_code";
        return exe_url($url);
    }
    function get_userinfo($code){
        $arr=$this->get_access_token($code);
        $openid=$arr['openid'];
        $token=$this->get_token();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$token&openid=$openid&lang=zh_CN";
        return exe_url($url);
    }
    public function auth_base($redirect){
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$redirect&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
        header("Location:$url");
    }
    public function get_openid($code){

    }
    private  function exe_url($url,$data=null){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($data){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

    /**
     * 根据订单信息生成签名
     * @param $data 订单信息
     * @return Weixin\json数据|null
     * @throws Weixin\WxPayException
     */
    public function jspay($data)
    {
        $jsApiParameters = null;
        if ($data) {
            $tools = new JsApiPay();
            $openId = session('openId');
            $input = new WxPayUnifiedOrder();
            $input->SetBody($data['body']);
            $input->SetAttach($data['attach']);
            $input->SetTotal_fee(intval($data['total_fee']));
            $input->SetGoods_tag($data['goods_tag']);
            $input->SetNotify_url($data['notify_url']);
            $input->SetOut_trade_no($data['order_id']);
            $input->SetTime_start($data['time']);
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = WxPayApi::unifiedOrder($input);
            $jsApiParameters = $tools->GetJsApiParameters($order);
        }
        return $jsApiParameters;
    }
}