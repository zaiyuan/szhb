<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Tools\upload\UploadLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OptionController extends Controller
{
    public function getConfig(Request $request)
    {
        $type=$request->input('type','');
        $name=$request->input('name','');
        $list=Option::getOptionByType($type,$name);
        return $this->success($list);
    }

    public function saveConfig(Request $request)
    {
        $params=$request->input();
        unset($params['s']);
        $uploalib=UploadLib::getUploadInstance();
        foreach($params as $name=>$value){
            $option=Option::where('name',$name)->first();
            if($option){
                if($option['value_type']=='image'){
                    $option->value=$uploalib->updateFile($option['value'],$value);
                }else{
                    $option->value=$value;
                }
                $option->save();
            }
        }

        //清除缓存
        Cache::forget(config('cache.option_cache'));
        return $this->success();
    }
}
