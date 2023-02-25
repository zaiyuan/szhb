<?php

namespace App\Http\Middleware;

use App\Helpers\JwtHelper;
use App\Models\system\Admin;
use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->header('access-token');
        if (empty($token)) {
            return response(['code' => 401, 'msg' => 'token无效'], 200);
        }

        $token = JwtHelper::verifyJwt($token);
        $adminId=$token->data->user_id;
        $admin = Admin::where('id',$adminId)->first();
        if(!$admin){
            return response(['code' => 401, 'msg' => '用户不存在，请重新登陆'], 200);
        }
        Auth::login($admin);

        return $next($request);
    }
}
