<?php
/**
 * User: qiaohao
 * Date: 2022/1/16
 * Time: 13:02
 */

namespace App\Tools\upload;



use app\common\lib\oss\AliOssLib;
use app\common\lib\StrLib;

class UploadOssLib extends UploadLib
{
    public $aliOss;
    public function __construct()
    {
        $this->aliOss=new AliOssLib();
    }

    public function fullImage($file)
    {
        if(!$file){
            return "";
        }
        if(substr($file,0,4)=="http"){
            return $file;
        }else{
            return $this->aliOss->getFullUrl($file);
        }
    }

    public function delFile($file)
    {
        $this->aliOss->deleteObject($file);
    }

    public function getPureObject($file)
    {
        $domain=$this->aliOss->getSiteUrl();
        $object=str_replace($domain.'/','',$file); //from_object
        return $object;
    }
    public function moveFile($file, $dir='')
    {
        if(!$file) return "";
        if(!$dir){
            $dir=date('Ym').'/'.date('d');
        }
        $from_object=$this->getPureObject($file); //from_object

        $basename=basename($file);
        $to_object=$dir.'/'.$basename;//to_object

        $res=$this->aliOss->copyObject($from_object,$to_object);
        if($res['success']){
            $this->aliOss->deleteObject($from_object);
            return $to_object;
        }
        return $from_object;
    }

    public function updateFile($old_file, $new_file, $dir="")
    {
        if(!$new_file){
            return $old_file;
        }
        $old_object=$this->getPureObject($old_file);
        $new_object=$this->getPureObject($new_file);

        if($old_object==$new_object){
            return $old_object;
        }
        if($old_object){
            $this->aliOss->deleteObject($old_object);
        }
        return $this->moveFile($new_object,$dir);
    }

    public function update_files($old_files, $new_files, $dir = '')
    {
        $dir=date("Ymd");
        $new_files=array_map(function ($e){
            return $this->getPureObject($e);
        },$new_files);

        $old_files=array_map(function ($e){
            return $this->getPureObject($e);
        },$old_files);

        $file_delete=array_diff($old_files,$new_files);
        foreach($file_delete as $file){
            $this->delFile($file);
        }

        foreach($new_files as $key=>$file){
            if(!in_array($file,$old_files)){
                $new_files[$key]=$this->moveFile($file,$dir);
            }
        }
        return $new_files;
    }


    public function save_binary_to_file($data, $ext, $uniName,$dir)
    {
        $object=$dir.'/'.$uniName.'.'.$ext;
        $res=$this->aliOss->putObject($object,$data);
        return $res;
    }

    public function copyFile($from_file, $dir = "")
    {
        if(!$from_file) return "";
        if(!$dir){
            $dir=date('Ym').'/'.date('d');
        }
        $from_object=$this->getPureObject($from_file); //from_object

        $pathinfo=pathinfo($from_object);
        $ext=$pathinfo['extension'];
        $filename=StrLib::getUniName();
        $to_object=$dir.'/'.$filename.'.'.$ext;

        $res=$this->aliOss->copyObject($from_object,$to_object);
        if($res['success']){
            return $to_object;
        }
        return $from_object;
    }


}
