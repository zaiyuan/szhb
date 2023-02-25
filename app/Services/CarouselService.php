<?php

namespace App\Services;

use App\Helpers\pageHelper;
use App\Models\Carousel;
use App\Tools\upload\UploadLib;
use Exception;
class CarouselService
{
    public function getList($params)
    {
        $pageParam=pageHelper::initPageParam($params['page'],$params['pageSize']);
        $where=[];
        if(isset($params['title']) && $params['title']){
            $where[]=['title','like',"%{$params['title']}%"];
        }
        if(isset($params['is_online']) && $params['is_online']){
            $where[]=['is_online','=',$params['is_online']];
        }
        $count=Carousel::where($where)->count();
        $list=Carousel::where($where)
            ->select('*')
            ->orderBy('sort','asc')
            ->orderBy('id','desc')
            ->offset($pageParam['offset'])
            ->limit($pageParam['pageSize'])
            ->get()
            ->toArray();
        return [
            'list'=>$list,
            'pagination'=>pageHelper::getPagination($params['page'],$params['pageSize'],$count)
        ];
    }

    //新增轮播图
    public function add($params)
    {
        $uploadLib=UploadLib::getUploadInstance();
        if(isset($params['image']) && $params['image']){
            $params['image']=$uploadLib->moveFile($params['image']);
        }
        Carousel::create($params);
    }

    //编辑
    public function update($params)
    {
        $uploadLib=UploadLib::getUploadInstance();

        $model=Carousel::find($params['id']);
        if(!$model){
            throw  new Exception("数据不存在");
        }
        if(isset($params['image']) && $params['image']){
            $params['image']=$uploadLib->updateFile($model['image'],$params['image']);
        }
        $model->fill($params);
        $model->save();
    }
}
