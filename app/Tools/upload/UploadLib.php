<?php
/**
 * User: qiaohao
 * Date: 2022/1/16
 * Time: 13:00
 */

namespace App\Tools\upload;


abstract class UploadLib
{
    private static $instance;
    private function __construct()
    {
    }
    private function __clone()
    {
    }

    public static function getUploadInstance($type = "")
    {
        if (!$type)
            $type = config('upload.type');
        if (!self::$instance) {
            if ($type == "local") {
                self::$instance = new UploadLocalLib();
            } else if ($type == 'ali_oss') {
                self::$instance = new UploadOssLib();
            }
        }
        return self::$instance;
    }

    //去掉域名，保留单纯的相对路径
    abstract public function getPureObject($file);
    //获取完整的链接
    abstract public function fullImage($file);

    //删除文件
    abstract public function delFile($file);

    //迁移文件 从临时目录到指定目录
    abstract public function moveFile($file, $dir);

    //更新单个文件，删除旧文件，迁移新文件
    abstract public function updateFile($old_file, $new_file, $dir);

    //更新文件数组
    abstract public function update_files($old_files,$new_files,$dir='');

    //保存二进制数据
    abstract public function save_binary_to_file($data, $ext, $uniName, $dir);

    //复制文件
    abstract public function copyFile($from_file,$dir="");
}
