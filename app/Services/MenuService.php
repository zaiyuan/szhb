<?php

namespace App\Services;

use App\Helpers\ArrayHelper;
use App\Models\system\SystemMenu;
use App\Models\system\SystemRole;

class MenuService
{
    //返回树型结构数据
    public function menuTree($params)
    {
        $where=[];
        if(isset($params['title']) && $params['title']){
            $where[]=['title','like',"%{$params['title']}%"];
        }

        $list=SystemMenu::select('*')
            ->where($where)
            ->orderBy('sort','asc')
            ->get()->toArray();

        $paths=[];
        foreach($list as $row){
            $paths[]=$row['path'];
        }

        foreach($list as $row){
            if($row['parent_id']!='0'  && !in_array($row['parent_id'],$paths)){
                $temp=SystemMenu::where('path',$row['parent_id'])->first();
                if($temp){
                    $list[]=$temp->toArray();
                }
            }
        }

        return ArrayHelper::listToTree($list,'path','parent_id','children','0');
    }

    //新增菜单
    public function addMenu($params)
    {
        foreach($params as $key=>$row){
            if(!$row){
                unset($params[$key]);
            }
        }
        SystemMenu::create($params);
    }

    //编辑菜单
    public function updateMenu($params)
    {
        $menu=SystemMenu::find($params['id']);
        if(!$menu){
            throw new \Exception("数据不存在");
        }
        unset($menu['id']);
        foreach($params as $key=>$value){
            $menu->$key=$value;
        }
        $menu->save();
    }

    //设置排序
    public function set_sort($id,$sort)
    {
        $menu=SystemMenu::find($id);
        if(!$menu){
            throw new \Exception("数据不存在");
        }
        $menu->sort=$sort;
        $menu->save();
    }

    //删除菜单
    public function delMenu($id)
    {
        $model=SystemMenu::find($id);
        if(!$model){
            throw new \Exception("数据不存在");
        }
        $child_count=SystemMenu::where('parent_id',$model['path'])->count();
        if($child_count>0){
            throw new \Exception("请先删除下级菜单");
        }
        $model->delete();
    }

    //获取一级菜单
    public function getTopMenus()
    {
        $menus=SystemMenu::where('parent_id',0)
            ->select('title as label','path as value')
            ->get()->toArray();
        array_unshift($menus,['label'=>'请选择','value'=>'0']);
        return $menus;
    }

    //根据管理员返回菜单
    public function getMenusByRole($role_id)
    {
        $role=SystemRole::find($role_id);
        if(!$role){
            throw new \Exception("角色不存在");
        }
        if($role->is_super==0){
            $menu_ids=explode(',',$role['act_list']);
            $parent_ids=SystemMenu::whereIn('id',$menu_ids)->distinct()->pluck('parent_id')->toArray();
            $menu_ids2=SystemMenu::whereIn('path',$parent_ids)->distinct()->pluck('id')->toArray();
            $menu_ids=array_merge($menu_ids,$menu_ids2);
            $menu_ids=array_map(function($e){
                return intval($e);
            },$menu_ids);
        }
        if($role->is_super==1){
            $menus=SystemMenu::orderBy('sort','asc')
                ->get()
                ->toArray();
        }else{
            $menus=SystemMenu::whereIn('id',$menu_ids)
                ->orderBy('sort','asc')
                ->get()
                ->toArray();
        }

        return $menus;
    }
}
