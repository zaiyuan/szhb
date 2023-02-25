<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\AdminRequest;
use App\Http\Requests\admin\LoginRequest;
use App\Models\AdminLog;
use App\Models\system\Admin;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->all();
        $res = (new AdminService())->login($data);
        return $this->success($res);
    }

    //管理员列表
    public function index(Request $request)
    {
        $params = $request->input();
        $res = (new AdminService())->getAdminList($params);
        return $this->success($res);
    }

    //管理员select数据
    public function all(Request $request)
    {
        $remove_super=$request->input('remove_super',0);
        $res = (new AdminService())->getAll($remove_super);
        return $this->success($res);
    }

    //添加管理员
    public function add(AdminRequest $request)
    {
        $params = $request->input();
        (new AdminService())->add($params);
        return $this->success();
    }

    //管理员详情
    public function detail($id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            throw new \Exception("数据不存在");
        }
        $admin->makeHidden('password');
        return $this->success($admin);
    }

    //编辑管理员
    public function update(AdminRequest $request)
    {
        $params = $request->input();
        (new AdminService())->update($params);
        return $this->success();
    }

    //删除管理员
    public function delete($id)
    {
        if ($id == 1) {
            throw new \Exception("admin管理员不能删除");
        }
        Admin::destroy($id);
        return $this->success();
    }

    public function set_status($id)
    {
        (new AdminService())->set_status($id);
        return $this->success();
    }

    //当前登陆用户修改密码
    public function password_edit(Request $request)
    {
        $params = $request->input();
        Validator::make($params, [
            'password' => 'required|confirmed'
        ], [
            'password.required' => '密码不能为空',
            'password.confirmed' => '密码和确认密码必须相同'
        ])->validate();

        $admin = $request->user();
        $admin->password = Crypt::encryptString($params['password']);
        $admin->save();
        return $this->success();
    }

    //管理员日志
    public function admin_log(Request $request)
    {
        $res = (new AdminService())->getAdminLog($request->input());
        return $this->success($res);
    }

    public function test()
    {
        $str = Crypt::encryptString(123456);
        return $str;
    }
}
