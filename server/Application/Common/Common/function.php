<?php

function get_token($appid,$appsecret){
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
    $jsoninfo = exe_url($url);
    $access_token = $jsoninfo["access_token"];
    return $access_token;
}
function get_userinfo($token,$openid){
    $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$token&openid=$openid&lang=zh_CN";
    dump(exe_url($url));
}
function get_all($token,$next_openid=null){
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
    dump(exe_url($url));
}
function create_menu($token,$menu){
    $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$token";
    $menu=array(array('button'=>array(
        'type'=>'click',
        'name'=>'aa',
        'key'=>'V1001_TODAY_MUSIC'
    ),
        array(
           array("name"=>"bb",
            "sub_button"=>array(
                array("type"=>"view",
                    "name"=>"vv",
                    "url"=>"http://wq.duguying.net/"
                ),
                array("type"=>"view",
                    "name"=>"百度",
                    "url"=>"http://www.soso.com/"
                )
            )
           )
        )
    ));

    $data=json_encode($menu,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    exe_url($url,$data);
}
function get_access_token($code){
    $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code";
}
function exe_url($url,$data=null){
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