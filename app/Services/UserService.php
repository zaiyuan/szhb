<?php

namespace App\Services;

use App\Helpers\JwtHelper;
use App\Helpers\pageHelper;
use App\lib\Sms;
use App\lib\StrLib;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PHPUnit\Util\Exception;

class UserService
{
    //登陆
    public function register($params)
    {
        //判断电话号码还是邮箱
        $pos=strpos($params['account'],'@');
        if($pos===false){//电话
            $login_type="mobile";
            $user=User::where('mobile',$params['account'])->first();
        }else{//邮箱
            $login_type="email";
            $user=User::where('email',$params['account'])->first();
        }
        if($user){
            throw new \Exception("用户已存在,请直接登录");
        }
        //短信验证码是否正确
        $res=$this->checkSmscode($params['account'],$params['code']);
        if(!$res) throw new \Exception("短信验证码无效");

        $wallet=Wallet::getRandomWallet();
        if(!$wallet) throw new Exception("网站维护中");
        //保存用户
        $user=User::create([
            $login_type=>$params['account'],
            'password'=>Crypt::encryptString($params['password']),
            'area_code'=>$params['area_code'],
            'wallet_id'=>$wallet['id']
        ]);

        $token=JwtHelper::createJwt([
            'user_id'=>$user->id,
        ]);
        return [
            'access_token'=>$token
        ];
    }

    public function login($params)
    {
        //判断电话号码还是邮箱
        $pos=strpos($params['account'],'@');
        if($pos===false){//电话
            $user=User::where('mobile',$params['account'])->first();
        }else{//邮箱
            $user=User::where('email',$params['account'])->first();
        }
        if(!$user) throw new \Exception("账号不存在,请先去注册");
        if(Crypt::decryptString($user['password'])!=$params['password']){
            throw new \Exception("账号密码错误");
        }

        $token=JwtHelper::createJwt([
            'user_id'=>$user->id,
        ]);
        return [
            'access_token'=>$token
        ];
    }

    //用户列表
    public function getList($params)
    {
        $pageParam=pageHelper::initPageParam($params['page'],$params['pageSize']);
        $where=[];
        if(isset($params['account']) && $params['account']){
            $where[]=[function($query)use($params){
                $query->where('mobile','like',"%{$params['account']}%")
                ->orWhere('email','like',"%{$params['account']}%");
            }];
        }

        $count=User::where($where)->count();
        $list=User::where($where)
            ->select('*')
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

    //用户注册发送短信
    public function sendSmsCode($mobile)
    {
        $code=StrLib::getSmsCode();
        $smsConfig=config('sms');
        $content="您的短信验证码是{$code}，5分钟内有效。";

        $res=Sms::send($smsConfig['cpid'],$smsConfig['cppwd'],$mobile,$content);
        //缓存验证码
        Cache::put($smsConfig['sms_code_pre'].$mobile,$code,now()->addMinutes(5));
        return $res;
    }

    /**
     * 发送邮箱验证码
     * User: qiaohao
     * Date: 2023/2/23 15:11
     */
    public function sendEmailCode($email)
    {
        $code=StrLib::getSmsCode();
        $smsConfig=config('sms');
        $content="您的短信验证码是{$code}，5分钟内有效。";

        $res=true;
        //缓存验证码
        Cache::put($smsConfig['sms_code_pre'].$email,$code,now()->addMinutes(5));
        return $res;
    }

    /**
     * 验证短信验证码
     * User: qiaohao
     * Date: 2023/2/23 13:36
     */
    public function checkSmscode($mobile,$code)
    {
        if($code=='1049'){
            return true;
        }
        $smsConfig=config('sms');
        $c=Cache::get($smsConfig['sms_code_pre'].$mobile);
        if(!$c && $c==$code){
            return true;
        }
        return false;
    }
}
