<?php

return [
    //测试号
//    'appid' => 'wx354492a52884969a',
//    'secret' => 'bef9791aef57cacf3d6515d7af5e857d',

    //正式号
    'appid'=>'wx1455b668f62e3c49',//公众号appid
    'secret'=>'aebd413d87aa0f33543aacc075d3e3c8',//公众号秘钥
    'mch_id' => '1605538290',//商户号
    'key'=>'7A5DED584C1F843903DDC1F91AEB68B4',//apiv2秘钥
    'api_key'=>'',//商户平台apiv3秘钥
    'cert_path'=>storage_path('cert/wechatpay/apiclient_cert.pem'),
    'key_path'=>storage_path('cert/wechatpay/apiclient_key.pem'),
];
