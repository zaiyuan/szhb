<?php

namespace App\Services;
//角色
use App\Helpers\pageHelper;
use App\Models\system\SystemRole;
use PHPUnit\Util\Exception;

class RoleService
{
    public function getRoleList($params)
    {
        $pageParam=pageHelper::initPageParam($params['page'],$params['pageSize']);

        $where=[];
        if(isset($params['name']) && $params['name']){
            $where[]=['name','like',"%{$params['name']}%"];
        }

        $count=SystemRole::where($where)->count();
        $list=SystemRole::where($where)
            ->select('id','name','desc','status')
            ->orderBy('id','asc')
            ->offset($pageParam['offset'])
            ->limit($pageParam['pageSize'])
            ->get()
            ->append('can_delete')->toArray();

        return [
            'list'=>$list,
            'pagination'=>pageHelper::getPagination($pageParam['page'],$pageParam['pageSize'],$count)
        ];
    }

    //新增角色
    public function add($params)
    {
        SystemRole::create($params);
    }

    //编辑角色
    public function update($params)
    {
        $model=SystemRole::find($params['id']);
        if(!$model){
            throw new Exception("数据不存在");
        }
        $model->name=$params['name'];
        $model->desc=$params['desc'];
        $model->act_list=is_array($params['act_list'])?json_encode($params['act_list']):$params['act_list'];
        $model->save();
    }
}
