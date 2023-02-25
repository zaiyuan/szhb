<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\RoleRequest;
use App\Models\system\SystemRole;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    //角色列表
    public function index(Request $request)
    {
       $params=$request->input();
       $res=(new RoleService())->getRoleList($params);
       return $this->success($res);
    }

    //所有角色，不分页
    public function all()
    {
        $list=SystemRole::select('id as value','name as label')
            ->orderBy('id','asc')
            ->get()->toArray();
        return $this->success($list);
    }

    //角色新增
    public function add(RoleRequest $request)
    {
        $params=$request->input();
        (new RoleService())->add($params);
        return $this->success();
    }

    //角色编辑
    public function update(RoleRequest $request)
    {
        $params=$request->input();
        (new RoleService())->update($params);
        return $this->success();
    }

    //角色删除
    public function delete($id)
    {
        if($id==1){
            throw new \Exception("超级管理员角色不能删除");
        }
        SystemRole::destroy($id);
        return $this->success();
    }

    //角色详情
    public function detail($id)
    {
        $model=SystemRole::find($id);
        return $this->success($model);
    }

    //设置状态
    public function set_status($id)
    {
        if($id==1){
            throw new \Exception("超级管理员不能禁用");
        }
        $model=SystemRole::find($id);
        $model->status=$model->status==1?0:1;
        $model->save();
        return $this->success();
    }
}
