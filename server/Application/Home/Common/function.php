<?php
function ajax($data){
    header('Content-Type:application/json; charset=utf-8');
    if($data){
        $list['list']=$data;
        exit(json_encode($list));
    }else{
        exit(json_encode(0));
    }
}
function get_url($url){
    return "http://".$_SERVER['HTTP_HOST'].'/ibaoh/'.$url;
}
function upload(){
    echo get_url('Uploads/');
}