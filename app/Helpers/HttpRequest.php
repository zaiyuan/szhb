<?php
/**
 * User: qiaohao
 * Date: 2021/2/1
 * Time: 14:39
 */

namespace App\Helpers;


use Exception;

class HttpRequest
{
    //url，请求的服务器地址
    private $url = '';

    //is_return，是否返回结果而不是直接显示
    private $is_return = 1;

    private $header=[];

    private $is_json=0;
    public function __set($p,$v)
    {
        if(property_exists($this, $p)){
            $this->$p = $v;
        }
    }
    // 发出http请求的方法
    //参数：提交的数据，默认是空的
    public function send($data = array())
    {
        //1. 如果传递数据了，说明向服务器提交数据(post)，如果没有传递数据，认为从服务器读取资源(get)
        $ch = curl_init();

        //2. 不管是get、post，跳过证书的验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        //3. 设置请求的服务器地址
        curl_setopt($ch, CURLOPT_URL, $this->url);

        //4. 判断是get还是post
        if(!empty($data)){
//            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');
            curl_setopt($ch, CURLOPT_POST, true);

            if($this->is_json==1 && is_array($data)){
                $data=json_encode($data);
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        //5。设置header
        if(!empty($this->header)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
            curl_setopt($ch, CURLOPT_HEADER, 0);//返回response头部信息
        }

        //6. 是否返回数据
        if($this->is_return===1){
            //说明返回
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            if(curl_error($ch)){
                throw new Exception(curl_error($ch));
            }
            curl_close($ch);
            return $result;
        }else{
            //直接输出
            curl_exec($ch);
            curl_close($ch);
        }
    }
}
