<?php
namespace App\Tools\wechat;

use App\Helpers\HttpRequest;

/**
 * 网页授权
 */
class LoginLib extends OfficialAccount
{
    /**
     * 获取授权链接
     * @param string $type base userinfo
     * @return string
     */
    public function authorize_link($superior_id)
    {
        $redirect_uri=env('APP_URL')."auth";
        $redirect_uri=urlencode($redirect_uri);
        $scope="snsapi_userinfo";
        $link="https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->appid}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state={$superior_id}#wechat_redirect";
        return $link;
    }

    /**
     * 获取用户授权access_token
     */
    public function oauth2_access_token($code)
    {
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->appsecret}&code={$code}&grant_type=authorization_code";
        $httpRequest=new HttpRequest();
        $httpRequest->url=$url;
        $res=$httpRequest->send();
        return json_decode($res,true);
//        "access_token": "65_lBn3A8ayEjtENoMfYcREi2PXv0gXenzpYkhgFm1PSghQFLxpLf2wNltyudiBhq-CzmbK5C6nxGGkX5z2Y38NbuIRTqWL9PsT80pxjdQv5vU",
//        "expires_in": 7200,
//        "refresh_token": "65_esXxEkS1_GNyOp5WqOvCskE2b_Gm4ydYtHH_ehw9-1LLOdkoglrMTfhIlQHyN9iWi_PW6B6ETxoYNJtA5cPyxsVJlGPLIE5NOaSaJhmJyRk",
//        "openid": "oC52e6i_ufEp7bNxPiRkhJl6-xn8",
//        "scope": "snsapi_userinfo"
    }

    /**
     * 拉取用户信息
     */
    public function userinfo($access_token,$openid)
    {
        $url="https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $httpRequest=new HttpRequest();
        $httpRequest->url=$url;
        $res=$httpRequest->send();
        return json_decode($res,true);
    }
}
