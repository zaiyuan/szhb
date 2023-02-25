<?php

namespace App\Helpers;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    //根据用户id等信息生成token加密串
    public static function createJwt($data, $second = 2592000)
    {
        $key = env('JWT_KEY'); //jwt的签发密钥，验证token的时候需要用到
        $time = time(); //签发时间
        $payload = array(
            "iss" => "http://www.lingser.com",//签发组织
            "aud" => "http://www.lingser.com", //接受者
            "iat" => $time,//签发时间
            "nbf" => $time,//生效时间
            "exp" => $time + $second,//过期时间
            'data' => $data//[user_id:1]
        );
        $jwt = JWT::encode($payload, $key,'HS256');
        return $jwt;
    }

    public static function verifyJwt($token)
    {
        $key = env('JWT_KEY');
        try {
            return JWT::decode($token, new Key($key, 'HS256'));
        } catch (ExpiredException $exception) {
            throw new \Exception('token过期', 401);
        } catch (\Exception $exception) {
            throw new \Exception('token无效', 401);
        }
    }
}
