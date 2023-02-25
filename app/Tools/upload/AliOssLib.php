<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: lhb
 * Date: 2017-05-19
 */

namespace App\Tools\upload;

use OSS\Core\OssException;
use OSS\OssClient;

require_once dirname(dirname(dirname(__DIR__))) . '/vendor/aliyuncs/oss-sdk-php/autoload.php';

/**
 * Class OssLogic
 * 对象存储逻辑类
 */
class AliOssLib
{
    static private $initConfigFlag = false;
    static private $accessKeyId = '';
    static private $accessKeySecret = '';
    static private $endpoint = '';
    static private $bucket = '';
    static private $host="";

    /** @var \OSS\OssClient */
    static private $ossClient = null;
    static private $errorMsg = '';


    public function __construct()
    {
        self::initConfig();
    }

    /**
     * 获取错误信息，一旦其他接口返回false时，可调用此接口查看具体错误信息
     * @return string
     */
    public function getError()
    {
        return self::$errorMsg;
    }

    static private function initConfig()
    {
        if (self::$initConfigFlag) {
            return;
        }

        $config = config('filesystems.disks.aliyunoss');
        self::$accessKeyId = $config['accessKeyId'] ?: '';
        self::$accessKeySecret = $config['accessKeySecret'] ?: '';
        self::$endpoint = $config['endpoint'] ?: '';
        self::$bucket = $config['bucket'] ?: '';
        self::$host = $config['host'] ?: '';
        self::$initConfigFlag = true;
    }

    //返回 client 实例
    static private function getOssClient()
    {
        if (!self::$ossClient) {
            self::initConfig();
            try {
                self::$ossClient = new OssClient(self::$accessKeyId, self::$accessKeySecret, self::$endpoint, false);
            } catch (OssException $e) {
                self::$errorMsg = "创建oss对象失败，" . $e->getMessage();
                return null;
            }
        }
        return self::$ossClient;
    }

    /**
     * 获取 前缀
     * @return string
     */
    public function getSiteUrl()
    {
        return "http://" . self::$host;
    }

    /**
     * 获取完整链接
     * @param $object
     * @return string
     */
    public function getFullUrl($object)
    {
        return $this->getSiteUrl() . '/' . $object;
    }

    //字符串上传
    public function putObject($object,$content)
    {
        $ossClient = self::getOssClient();
        if (!$ossClient) {
            return false;
        }

        try {
            $ossClient->putObject(self::$bucket, $object, $content);

        } catch (OssException $e) {
            self::$errorMsg = "oss上传字符串失败，" . $e->getMessage();
            return false;
        }
        return $object;
    }

    /**
     * @param $filePath string 待上传文件的绝对路径
     * @param null $object 要保存的对象名称，文件夹+文件名
     * @return false|string
     */
    public function uploadFile($filePath, $object = null)
    {
        $ossClient = self::getOssClient();
        if (!$ossClient) {
            return false;
        }

        if (is_null($object)) {
            $object = $filePath;
        }

        try {
            $ossClient->uploadFile(self::$bucket, $object, $filePath);
        } catch (OssException $e) {
            self::$errorMsg = "oss上传文件失败，" . $e->getMessage();
            return false;
        }

        return $this->getSiteUrl() . '/' . $object;
    }


    /**
     * 删除文件
     * @param $object
     * @return array|false|string
     */
    public function deleteObject($object)
    {
        $ossClient = self::getOssClient();
        if (!$ossClient) {
            return false;
        }
        try {
            $res = $ossClient->deleteObject(self::$bucket, $object);
            return [
                'success' => true,
                'msg' => 'success',
                'data' => $res
            ];
        } catch (OssException $e) {
            return $e->getMessage();
        }
    }

    public function copyObject($object, $to_object)
    {
        $ossClient = self::getOssClient();
        if (!$ossClient) {
            return false;
        }
        try {
            $res = $ossClient->copyObject(self::$bucket, $object, self::$bucket, $to_object);
            return [
                'success' => true,
                'msg' => 'success',
                'data' => $res
            ];
        } catch (OssException $e) {
            return [
                'success' => false,
                'msg' => $e->getMessage(),
                'data' => ''
            ];
        }
    }

    /**
     * 下载文件
     */
    public function getObject($object)
    {
        $ossClient = self::getOssClient();
        if (!$ossClient) {
            return false;
        }

        try {
            $res = $ossClient->getObject(self::$bucket, $object);
            return [
                'success' => true,
                'msg' => 'success',
                'data' => $res
            ];
        } catch (OssException $e) {
            return [
                'success' => false,
                'msg' => $e->getMessage()
            ];
        }
    }

    /**
     * 是否是oss的链接
     */
    public function isOssUrl($url)
    {
        if ($url && strpos($url, 'http') === 0 && strpos($url, 'aliyuncs.com')) {
            return true;
        }
        return false;
    }

    function gmt_iso8601($time)
    {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }

    //前端直传
    public function signature()
    {
        $id = self::$accessKeyId;          // 请填写您的AccessKeyId。
        $key = self::$accessKeySecret;     // 请填写您的AccessKeySecret。
        $host = self::$bucket . '.' . self::$endpoint;// $host的格式为 bucketname.endpoint，请替换为您的真实信息。
        $callbackUrl = '';  // $callbackUrl为上传回调服务器的URL，请将下面的IP和Port配置为您自己的真实URL信息。
        $dir = '';          // 用户上传文件时指定的前缀。

        $callback_param = array('callbackUrl' => $callbackUrl,
            'callbackBody' => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType' => "application/x-www-form-urlencoded");
        $callback_string = json_encode($callback_param);

        $base64_callback_body = base64_encode($callback_string);
        $now = time();
        $expire = 30;  //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问。
        $end = $now + $expire;
        $expiration = $this->gmt_iso8601($end);


        //最大文件大小.用户可以自己设置
        $condition = array(0 => 'content-length-range', 1 => 0, 2 => 1048576000);
        $conditions[] = $condition;

        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
        $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
        $conditions[] = $start;


        $arr = array('expiration' => $expiration, 'conditions' => $conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        $response = array();
        $response['accessid'] = $id;
        $response['host'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['callback'] = $base64_callback_body;
        $response['dir'] = $dir;  // 这个参数是设置用户上传文件时指定的前缀。
        return $response;
    }

    //判断文件是否存在
    public function isExist($object)
    {
        $ossClient = self::getOssClient();
        if (!$ossClient) {
            return false;
        }
        try {
            $res = $ossClient->doesObjectExist(self::$bucket, $object);
            return [
                'success' => true,
                'msg' => 'success',
                'data' => $res
            ];
        } catch (OssException $e) {
            return [
                'success' => true,
                'msg' => $e->getMessage(),
            ];
        }
    }
}
