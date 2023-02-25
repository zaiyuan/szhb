<?php
/**
 * User: qiaohao
 * Date: 2021/7/17
 * Time: 17:56
 */

namespace App\Tools\wechat;

//微信公众号超类
use App\Helpers\HttpRequest;
use App\lib\StrLib;
use Illuminate\Support\Facades\Cache;

class OfficialAccount
{
    protected $appid;
    protected $appsecret;

    public function __construct()
    {
        $this->appid=config('wechat.appid');
        $this->appsecret=config('wechat.secret');
    }

    /**
     * 校验服务器
     * @return bool
     */
    public static function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = "lingser";
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    //获取公众号基础 access_token
    private function token()
    {
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->appsecret}";
        $httpRequest=new HttpRequest();
        $httpRequest->url=$url;
        $res=$httpRequest->send();
        $res= json_decode($res,true);
        if(isset($res['errcode']) && $res['errcode']!=0){
            throw new \Exception($res['errmsg']);
        }
        return $res;
    }

    //公众号基础 access_token缓存
    public function getTokenCache()
    {
        $access_token=Cache::get('officialaccount_access_token');
        if($access_token){
            return $access_token;
        }

        $res=$this->token();
        Cache::put('officialaccount_access_token',$res['access_token'],now()->addMinutes(100));
        return $res['access_token'];
    }

    //获取jsapi_ticket
    public function getticket()
    {
        $access_token=$this->getTokenCache();
        $url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=jsapi";
        $httpRequest=new HttpRequest();
        $httpRequest->url=$url;
        $res=$httpRequest->send();
        if(isset($res['errcode']) && $res['errcode']!=0){
            throw new \Exception($res['errmsg']);
        }
        return json_decode($res,true);
    }

    //缓存jsapi_ticket
    public function getticketcache()
    {
        $ticket=Cache::get('officialaccount_ticket');
        if($ticket){
            return $ticket;
        }

        $res=$this->getticket();
        Cache::put('officialaccount_ticket',$res['ticket'],now()->addMinutes(100));
        return $res['ticket'];
    }

    //生成jssdk签名
    public function jssdk_signature($url)
    {
       $noncestr=StrLib::genRandomString(16);
       $jsapi_ticket=$this->getticketcache();
       $timestamp=time();
       $arr=compact('jsapi_ticket','noncestr','timestamp','url');
       $str=StrLib::ToUrlParams($arr);
       $signature= sha1($str);
       return [
           'appId'=>$this->appid,
           'timestamp'=>$timestamp,
           'nonceStr'=>$noncestr,
           'signature'=>$signature,
       ];
    }
}
