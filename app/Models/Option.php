<?php

namespace App\Models;

use App\Tools\upload\UploadLib;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Option extends Model
{
    use HasFactory;
    protected $table="option";
    const TYPE_SYSTEM="system";
    public $timestamps=false;

    /**
     * 根据条件获取配置列表
     * User: qiaohao
     * Date: 2023/2/23 19:59
     */
    public static function getOptionByType($type='',$name='')
    {
        $where=[];
        if($type){
            $where[]=['type','=',$type];
        }
        if($name){
            $where[]=['name','=',$name];
        }
        $list=Option::where($where)
            ->get()
            ->toArray();
        return array_column($list,'value','name');
    }

    //获取缓存
    public static function getCacheConfig()
    {
        $key=config('cache.option_cache');
        if(Cache::has($key)){
            $res=Cache::get($key);
            $list= json_decode($res,true);
        }else{
            $list=Option::select('name','value')->get()->toArray();
            $list=array_column($list,'value','name');
            Cache::put($key,json_encode($list));
        }
        if(isset($list['exchange_cover'])){
            $uploadlib=UploadLib::getUploadInstance();
            $list['exchange_cover']=$uploadlib->fullImage($list['exchange_cover']);
        }
        return $list;
    }
}
