<?php
/**
 * User: qiaohao
 * Date: 2022/1/16
 * Time: 13:02
 */

namespace App\Tools\upload;


use app\common\lib\StrLib;
use App\Helpers\UploadHelper;
use Exception;

class UploadLocalLib extends UploadLib
{
    //获取完整链接
    public function fullImage($file)
    {
        if(!$file) return "";
        if(substr($file,0,4)=='http'){
            return $file;
        }else{
            $file=parse_url($file)['path'];
            return env('APP_URL').$file;
        }
    }

    //获取绝对路径
    public function getAbsolutePath($file)
    {
        if($file){
            $file=$this->getPureObject($file);
            return public_path().'/'.$file;
        }
        return $file;
    }

    //删除文件
    public function delFile($file)
    {
        if($file){
            $file=$this->getAbsolutePath($file);
            if(file_exists($file)){
                unlink($file);
            }
        }
    }

    public function moveFile($file,$dir='')
    {
        if(!$file) return "";

        $source = public_path().'/'.$file;

        if (!file_exists($source)) {
            throw new Exception("文件不存在");
        }

        //目的文件夹
        $date=date("Ym",time());
        $destination_dir = public_path().'/upload/'.$date.'/';
        if (!is_dir($destination_dir)) {
            mkdir($destination_dir, 0777, true);
        }

        //目的文件
        $filename=basename($file);
        $destination = $destination_dir . $filename;//目的文件

        $res = copy($source, $destination);
        if (!$res) {
            throw new Exception("copy失败");
        }
        return "upload/".$date.'/'.$filename;
    }

    public function updateFile($old_file, $new_file, $dir='')
    {
        $dir=date('Ymd');
        if(!$new_file){
            return $old_file;
        }
        $old_file_arr=parse_url($old_file);
        $old_file=$old_file_arr['path'];
        $old_file=ltrim($old_file,'/');

        $new_file_arr=parse_url($new_file);
        $new_file=$new_file_arr['path'];
        $new_file=ltrim($new_file,'/');

        if($old_file==$new_file){
            return $old_file;
        }

        if($old_file){
            $this->delFile($old_file);
        }
        return $this->moveFile($new_file,$dir);
    }


    //更新多张图片 多张图片在一个字段保存
    public function update_files($old_files,$new_files,$dir='')
    {
        $dir=date("Ymd");
        $new_files=array_map(function ($e){
            return ltrim(parse_url($e)['path'],'/');
        },$new_files);

        $old_files=array_map(function ($e){
            return ltrim(parse_url($e)['path'],'/');
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

    //保存二进制文件到指定目录
    public function save_binary_to_file($data,$ext,$uniName,$dir="")
    {
        $dir=date('Ymd');
        $dest_dir=WEB_ROOT.'upload/'.$dir.'/';
        if(!is_dir($dest_dir)){
            mkdir($dest_dir,0777,true);
        }
        $uniName=$uniName?$uniName:StrLib::getUniName();
        $filename=$uniName.'.'.$ext;
        $object="/upload/".$dir.'/'.$filename;
        $object_realpath=$dest_dir.'/'.$filename;

        $file=fopen($object_realpath,'w');
        fwrite($file,$data);
        fclose($file);
        return $object;
    }

    public function copyFile($from_file, $dir = "")
    {

    }

    public function getPureObject($file)
    {
        $file=parse_url($file)['path'];
        $file=ltrim($file,'/');
        return $file;
    }

    //保存base64为文件
    public function base64_image_content($base64_image_content){
        if(preg_match('/^(data:image\/svg\+xml;base64,)/', $base64_image_content,$result)){
            $type = "svg";

            //目的文件夹
            $date=date("Ym",time());
            $destination_dir = public_path().'/upload/'.$date.'/';
            if (!is_dir($destination_dir)) {
                mkdir($destination_dir, 0777, true);
            }
            $filename=(new UploadHelper())->getUniName();
            $new_file = $destination_dir.$filename.".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
                return "upload/{$date}/{$filename}.{$type}";
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
