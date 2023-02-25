<?php

namespace App\Http\Controllers\admin;

use App\Helpers\ArrayHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\MenuRequest;
use App\Models\system\SystemMenu;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Exception;
class MenuController extends Controller
{
    //所有菜单节点-以树型结构返回
    public function menuTree(Request $request)
    {
        $params=$request->input();
        $res=(new MenuService())->menuTree($params);
        return $this->success($res);
    }

    //菜单新增
    public function menuAdd(MenuRequest $request)
    {
        $params=$request->post();
        (new MenuService())->addMenu($params);
        return $this->success([]);
    }

    //菜单详情
    public function menuDetail($id)
    {
        $menu=SystemMenu::find($id);
        if(!$menu){
            throw new Exception("数据不存在");
        }
        return $this->success($menu->toArray());
    }

    //菜单编辑
    public function menuUpdate(MenuRequest $request)
    {
        $params=$request->post();
        (new MenuService())->updateMenu($params);
        return $this->success([]);
    }

    //删除菜单
    public function menuDel($id)
    {
        (new MenuService())->delMenu($id);
        return $this->success();
    }

    //设置排序
    public function set_sort($id,$sort)
    {
        (new MenuService())->set_sort($id,$sort);
        return $this->success([]);
    }

    //管理员菜单
    public function current_menu(Request $request)
    {
        $admin=$request->user();
        $menus=(new MenuService())->getMenusByRole($admin['role_id']);
        foreach($menus as $key=>$menu){
            $menus[$key]['meta']=['title'=>$menu['title']];
            $menus[$key]['hidden']=$menu['cate']==1?false:true;

            unset($menus[$key]['title']);
            unset($menus[$key]['cate']);
            unset($menus[$key]['id']);
            unset($menus[$key]['sort']);
            unset($menus[$key]['descriptions']);
        }

        $menuTree=ArrayHelper::listToTree($menus,'path','parent_id','children','0');
        return $this->success($menuTree);
    }

    //所有的一级菜单
    public function top_menu()
    {
        $list=(new MenuService())->getTopMenus();
        return $this->success($list);
    }
}
