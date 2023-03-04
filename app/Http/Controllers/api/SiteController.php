<?php

namespace App\Http\Controllers\api;

use App\Helpers\EmailHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\FindPasswordRequest;
use App\Http\Requests\api\LoginRequest;
use App\Http\Requests\api\RegisterRequest;
use App\Http\Requests\api\SendCodeRequest;
use App\lib\StrLib;
use App\Models\User;
use App\Services\UserService;
use App\Tools\upload\UploadLib;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class SiteController extends Controller
{
    /**
     * 前端用户注册
     * User: qiaohao
     * Date: 2023/2/23 11:45
     */
    public function register(RegisterRequest $request)
    {
        $params = $request->input();
        $res = (new UserService())->register($params);
        return $this->success($res);
    }

    /**
     * 前端用户登陆（账号+密码）
     * User: qiaohao
     * Date: 2023/2/23 14:59
     */
    public function login(LoginRequest $request)
    {
        $params = $request->input();
        $res = (new UserService())->login($params);
        return $this->success($res);
    }

    /**
     * 发送短信或者邮箱验证码接口
     * User: qiaohao
     * Date: 2023/2/23 11:46
     */
    public function sendCode(SendCodeRequest $request)
    {
        try{
            $params=$request->input();
            if ($params['login_type'] === 'mobile') {//电话
                $res = (new UserService())->sendSmsCode($params['area_code'].$params['mobile']);
            } else {//邮箱
                $res = (new UserService())->sendEmailCode($params['email']);
            }
            return $this->success($res);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }


    /**
     * 找回密码
     * User: qiaohao
     * Date: 2023/2/23 15:16
     */
    public function findPassword(FindPasswordRequest $request)
    {
        $params=$request->input();
        if($params['login_type']=='mobile'){
            $res=(new UserService())->checkSmscode($params['area_code'].$params['mobile'],$params['code']);
        }else{
            $res=(new UserService())->checkSmscode($params['email'],$params['code']);
        }
        if(!$res){
            throw new Exception("验证码无效");
        }
        $user=User::where($params['login_type'],$params[$params['login_type']])->first();
        if(!$user){
            throw new Exception("账号不存在");
        }
        $user->password=Crypt::encryptString($params['password']);
        $user->save();
        return $this->success([]);
    }

    /**
     * 获取用户信息
     */
    public function userinfo()
    {
        $user = Auth::user();
        return $this->success([
            'id' => $user['id'],
            'nickname' => $user['nickname'],
            'avatar' => $user['avatar'],
            'mobile' => $user['mobile'],
        ]);
    }

    /**
     * 修改用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function userinfo_modify(Request $request)
    {
        $user = Auth::user();
        $params = $request->input();

        $uploadLib = UploadLib::getUploadInstance();
        if (isset($params['avatar']) && $params['avatar']) {
            $user->avatar = $uploadLib->moveFile($params['avatar']);
        }

        if (isset($params['nickname']) && $params['nickname']) {
            $user->nickname = $params['nickname'];
        }

        if (isset($params['mobile']) && $params['mobile']) {
            $user->mobile = $params['mobile'];
        }
        $user->save();
        return $this->success();
    }
}
