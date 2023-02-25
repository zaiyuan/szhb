<?php
/**
 * User: qiaohao
 * Date: 2021/7/17
 * Time: 19:24
 */

namespace App\lib;


class StrLib
{
    //生成订单号
    public static function generate_ordersn($pre="")
    {
        return $pre.date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    /**
     * 产生一个指定长度的随机字符串,并返回给用户
     * @param  $len int 产生字符串的长度
     * @return string 随机字符串
     */
    public static function genRandomString($len = 32) {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
        // 将数组打乱
        shuffle($chars);
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }

    /**
     * 将参数拼接为url: key=value&key=value
     * @param $params
     * @return string
     */
    public static function ToUrlParams($params){
        $string = '';
        if( !empty($params) ){
            $array = array();
            foreach( $params as $key => $value ){
                if(!empty($value)){
                    $array[] = $key.'='.$value;
                }
            }
            $string = implode("&",$array);
        }
        return $string;
    }

    /**
     * 短信验证码
     * User: qiaohao
     * Date: 2023/2/23 11:28
     */
    public static function getSmsCode()
    {
        return mt_rand(1000,9999);
    }

    /**
     * 账号类型
     * @param $account
     * @return string mobile,email
     * User: qiaohao
     * Date: 2023/2/23 15:39
     */
    public static function getAccountType($account)
    {
        $pos=strpos($account,'@');
        return $pos===false?"mobile":"email";
    }
}
