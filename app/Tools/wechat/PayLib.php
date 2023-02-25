<?php

namespace App\Tools\wechat;

use App\lib\StrLib;

class PayLib extends OfficialAccount
{
    private $mch_id;//商户号
    private $key;//apiv2秘钥
    private $cert_path;//证书
    private $key_path;
    private $unifiedorderUrl="https://api.mch.weixin.qq.com/pay/unifiedorder"; //统一下单接口
    private $refundurl="https://api.mch.weixin.qq.com/secapi/pay/refund"; //退款接口

    public function __construct()
    {
        parent::__construct();
        $this->mch_id=config('wechat.mch_id');
        $this->key=config('wechat.key');
        $this->cert_path=config('wechat.cert_path');
        $this->key_path=config('wechat.key_path');
    }
    //生成签名
    public function MakeSign($params){
        if(isset($params['sign'])){
            unset($params['sign']);
        }
        //签名步骤一：按字典序排序数组参数
        ksort($params);
        $string = StrLib::ToUrlParams($params);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$this->key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    //数组转化为xml
    protected function data_to_xml( $params ){
        if(!is_array($params)|| count($params) <= 0)
        {
            return false;
        }
        $xml = "<xml>";
        foreach ($params as $key=>$val)
        {
            $xml.="<".$key.">".$val."</".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }

    //xml转化为数组
    protected function xml_to_data($xml){
        if(!$xml){
            return false;
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }

    /**
     * 以post方式提交xml到对应的接口url
     * @param string $xml 需要post的xml数据
     * @param string $url url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second url执行超时时间，默认30s
     * @throws WxPayException
     */
    private function postXmlCurl($xml, $url, $useCert = false, $second = 30){
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $this->cert_path);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $this->key_path);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果,有数据返回数据，没有数据返回false
        if($data){
            curl_close($ch);
            return $data;
        } else {
            curl_close($ch);
            return false;
        }
    }

    /**
     * 生成公众号支付参数
     * @param $prepayid 预支付id
     */
    private function getWebPayParams( $prepayid){
        $data['appId'] = $this->appid;
        $data['nonceStr'] = StrLib::genRandomString();
        $data['package'] = 'prepay_id='.$prepayid;
        $data['signType'] = 'MD5';
        $data['timeStamp'] = time().'';
        $data['paySign'] = $this->MakeSign( $data );
//        $data['result_code'] = $result_code;
        return $data;
    }
    public function weChatOrder($data){

        $data['appid'] = $this->appid;  //应用ID
        $data['mch_id'] = $this->mch_id;  //商户号
        $data['nonce_str'] = StrLib::genRandomString();  //随机字符串
        $data['sign_type']="MD5";

        $data['spbill_create_ip'] = $data['ip'];  //用户端IP
        unset($data['ip']);
        $data['trade_type'] = 'JSAPI';  //调用支付方式

        $sign = strtoupper($this->MakeSign($data));
        $data['sign'] = $sign;//签名
        $xml = $this->data_to_xml($data);
        $response = $this->postXmlCurl($xml, $this->unifiedorderUrl);

        if( !$response ){
            return [
                'success'=>false,
                'msg'=>'curl没有返回数据'
            ];
        }
        $result = $this->xml_to_data( $response );

        if($result['return_code'] == 'FAIL'){
            return [
                'success'=>false,
                'msg'=>$result['return_msg']
            ];
        }else if($result['result_code'] == 'FAIL'){
            return [
                'success'=>false,
                'msg'=>$result['err_code']
            ];
        }
        $resultData = $this->getWebPayParams( $result['prepay_id']);//,$result['result_code']
        return [
            'success'=>true,
            'data'=>$resultData
        ];
    }

    //退款
    public function refund($data)
    {
        $data['appid']=$this->appid;
        $data['mch_id']=$this->mch_id;
        $data['nonce_str']=StrLib::genRandomString();
        $data['notify_url']="";


        $sign = strtoupper($this->MakeSign($data));
        $data['sign'] = $sign;//签名

        $xml = $this->data_to_xml($data);
        $response = $this->postXmlCurl($xml, $this->refundurl,true);

        if( !$response ){
            return [
                'success'=>false,
                'msg'=>'curl没有返回数据'
            ];
        }
        $result = $this->xml_to_data( $response );

        if($result['return_code'] == 'FAIL'){
            return [
                'success'=>false,
                'msg'=>$result['return_msg']
            ];
        }else if($result['result_code'] == 'FAIL'){
            return [
                'success'=>false,
                'msg'=>$result['err_code']
            ];
        }

        return [
            'success'=>true,
            'data'=>$result,
            'msg'=>'success'
        ];
    }
}
