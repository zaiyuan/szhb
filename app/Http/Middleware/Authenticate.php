<?php

namespace App\Http\Middleware;

use App\Helpers\JwtHelper;
use App\Models\Admin;
use App\Models\User;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
   public function handle($request, Closure $next, ...$guards)
   {
       $token = $request->header('access-token');
       if (empty($token)) {
           return response(['code' => 401, 'msg' => 'token无效'], 200);
       }

       $token = JwtHelper::verifyJwt($token);
       $adminId=$token->data->user_id;
       $admin = User::where('id',$adminId)->first();
       if(!$admin){
           return response(['code' => 401, 'msg' => '用户不存在，请重新登陆'], 200);
       }
       Auth::login($admin);
       return $next($request);
   }
}
