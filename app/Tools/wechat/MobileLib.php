<?php

namespace App\Tools\wechat;

use App\Helpers\HttpRequest;

class MobileLib extends OfficialAccount
{
    public function getMobile($code)
    {
        $accessToken=$this->getTokenCache();
        $url="https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token={$accessToken}";

        $httpRequest=new HttpRequest();
        $httpRequest->url=$url;
        $httpRequest->is_json=1;
        $res=$httpRequest->send([
            'code'=>$code
        ]);
        $res=json_decode($res,true);
        if($res['errcode']!=0){
            throw new \Exception($res['errmsg']);
        }
        return $res['phone_info']['phoneNumber'];
    }
}
