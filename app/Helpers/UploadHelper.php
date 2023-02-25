<?php
/**
 * User: qiaohao
 * Date: 2020/4/18
 * Time: 14:44
 */

namespace App\Helpers;

use App\Tools\upload\AliOssLib;
use App\Tools\upload\UploadLib;

class UploadHelper
{
    protected $original_name="";
    protected $fileName = 'file';//字段名称
    protected $uploadPath;//上传路径 local
    protected $upload_dir;//oss
    protected $object;//数据库存储的内容
    protected $fileInfo;//$_FILE[$filename]
    protected $ext;//后缀
    protected $error = "";//错误信息
    protected $full_url;//
    protected $uniName;//保存的文件名
    protected $filetype = "image";

    public function __set($p,$v)
    {
        if(property_exists($this, $p)){
            $this->$p = $v;
        }
    }

    /**
     * 检查文件上传是否出错
     * @return bool
     */
    protected function checkError()
    {
        if ($this->fileInfo['error'] > 0) {
            switch ($this->fileInfo['error']) {
                case 1:
                    $this->error = "超过了php配置文件中的upload_max_filesize选项的值";
                    break;
                case 2:
                    $this->error = "超过了表单中MAX_FILE_SIZE设置的值";
                    break;
                case 3:
                    $this->error = "文件部分被上传";
                    break;
                case 4:
                    $this->error = "没有选择上传的文件";
                    break;
                case 6:
                    $this->error = "没有找到临时目录";
                    break;
                case 7:
                    $this->error = "文件不可泄";
                    break;
                case 8:
                    $this->error = "由于php的扩展程序中断文件上传";
                    break;
            }
            return false;
        }

        return true;
    }

    /**
     * 检测上传文件大小
     * @return bool
     */
    protected function checkSize()
    {
        $sizeArr = config('upload.size_arr');

        //根据文件后缀获取文件类型
        $this->getFileTypeByExt();

        $maxSize = $sizeArr[$this->filetype] * 1024 * 1024;
        if ($this->fileInfo['size'] > $maxSize) {
            $this->error = "上传文件大小不能超过" . $sizeArr[$this->filetype].'M';
            return false;
        }
        return true;
    }

    public function getFileTypeByExt()
    {
        $suffix_arr = config('upload.suffix_arr');
        foreach($suffix_arr as $key=>$row){
            if(strpos($row,$this->ext)!==false){
                $this->filetype=$key;
                break;
            }
        }
        return true;
    }

    /**
     * 检测上传文件的扩展名
     * @return bool
     */
    protected function checkExt()
    {
        //获取上传文件的后缀
        $this->ext = strtolower(pathinfo($this->fileInfo['name'], PATHINFO_EXTENSION));
        //判断文件后缀是否可以上传
        $allowExt = $this->getExts();
        if (!in_array($this->ext, $allowExt)) {
            $this->error = "不允许的扩展名" . $this->ext;
            return false;
        }
        return true;
    }

    //获取可以上传后缀
    public function getExts()
    {
        $suffix_arr = config('upload.suffix_arr');
        $str="";
        foreach($suffix_arr as $row){
            $str.=$row.',';
        }
        $str=rtrim($str,',');
        return explode(',',$str);
    }

    /**
     * 检测是否是真实图片
     * @return bool
     */
    protected function checkTrueImg()
    {
        if ($this->filetype == 'image') {
            if (!@getimagesize($this->fileInfo['tmp_name'])) {
                $this->error = "不是真实图片";
                return false;
            }
        }
        return true;
    }

    /**
     * 检测是否是通过HTTP POST 方式上传上来的
     * @return bool
     */
    protected function checkHTTPPost()
    {
        if (!is_uploaded_file($this->fileInfo['tmp_name'])) {
            $this->error = "文件不是通过HTTP POST方式上传上来的";
            return false;
        }
        return true;
    }

    /**
     * 检测目录不存在则创建
     */
    protected function checkUploadPath()
    {
        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }
    }

    /**
     * 产生唯一字符串
     * @return string
     */
    public function getUniName()
    {
        return md5(uniqid(microtime(true), true) . rand(1000, 9999));
    }

    /**
     * 上传文件
     * @return array
     */
    public function uploadFile()
    {
        if(!isset($_FILES[$this->fileName])){
            return [
                'success' => false,
                'msg' => "请上传文件"
            ];
        }
        $this->fileInfo = $_FILES[$this->fileName];
        if ($this->checkError() && $this->checkExt() && $this->checkSize() && $this->checkTrueImg() && $this->checkHTTPPost()) {
            $this->original_name=$this->fileInfo['name'];
            $this->uniName = $this->getUniName();
            if (config('upload.type') == 'local') {
                $this->object='upload/'.request()->file($this->fileName)->store(($this->upload_dir?$this->upload_dir.'/':"").date('Ym'));
                $this->full_url= (UploadLib::getUploadInstance())->fullImage($this->object);
            } else if (config('upload.type') == 'ali_oss') {
                $this->object = 'upload/'.($this->upload_dir?$this->upload_dir.'/':"").date('Ym').'/' . $this->uniName . '.' . $this->ext;
                $osslogic=new AliOssLib();
                $res=$osslogic->uploadFile($this->fileInfo['tmp_name'],$this->object);
                if(!$res){
                    $this->error=$osslogic->getError();
                }
                $this->full_url=$res;
            }
        }

        if ($this->error) {
            return [
                'success' => false,
                'msg' => $this->error
            ];
        } else {
            return [
                'success' => true,
                'msg' => $this->error,
                'data' => [
                    'filename' => $this->object,
                    'filepath' => $this->full_url,
                    'original_name'=>$this->original_name
                ]
            ];
        }
    }

}
