<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\AddCurrencyRequest;
use App\Http\Requests\admin\FreezeRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    //用户列表
    public function index(Request $request)
    {
        $params=$request->input();
        $res=(new UserService())->getList($params);
        return $this->success($res);
    }

    //用户详情
    public function detail($id)
    {
        $user=User::find($id);
        $user->append([
            'currency_list'
        ]);
        return $this->success($user);
    }

    //用户删除
    public function delete($id)
    {
        User::destroy($id);
        return $this->success();
    }

    public function setStatus($id)
    {
        $user=User::find($id);
        $user->status=$user->status==1?0:1;
        $user->save();
        return $this->success();
    }

    //清除缓存
    public function clearCache()
    {
        Cache::flush();
        return $this->success("清除成功");
    }

    /**
     * 修改密码
     * User: qiaohao
     * Date: 2023/2/22 22:24
     */
    public function passwordEdit(Request $request)
    {
        $id=$request->input('id');
        $password=$request->input('password');
        if(!$password){
            throw new \Exception("密码不能为空");
        }
        $user=User::find($id);
        $user->password=Crypt::encryptString($password);
        $user->save();
        return $this->success();
    }

    /**
     * 币种加减余额
     * User: qiaohao
     * Date: 2023/2/22 22:42
     */
    public function addCurrency(AddCurrencyRequest $request)
    {
        $params=$request->input();
        if($params['addValue']<0){
            throw new \Exception("操作金额必须大于0");
        }

        $user=User::find($params['user_id']);
        $balance_field=$params['currency_type'].'_balance';
        if($params['plus_or_minus']=='plus'){
            $user->increment($balance_field,$params['addValue']);
        }else{
            if($user->$balance_field<$params['addValue']){
                throw new \Exception("用户余额不够扣除");
            }
            $user->decrement($balance_field,$params['addValue']);
        }
        return $this->success([]);
    }

    /**
     * 冻结或者解冻金额
     * User: qiaohao
     * Date: 2023/2/24 16:18
     */
    public function freeze(FreezeRequest $request)
    {
        $params=$request->input();
        if($params['addValue']<0){
            throw new \Exception("操作金额必须大于0");
        }

        $user=User::find($params['user_id']);
        $balance_field=$params['currency_type'].'_balance';
        $frozen_field=$params['currency_type'].'_frozen';
        if($params['plus_or_minus']=='frozen'){//冻结
            if($user->$balance_field<$params['addValue']){
                throw new \Exception("余额不足");
            }
            $user->decrement($balance_field,$params['addValue']);
            $user->increment($frozen_field,$params['addValue']);

        }else{//解冻
            if($user->$frozen_field<$params['addValue']){
                throw new \Exception("余额不足");
            }
            $user->decrement($frozen_field,$params['addValue']);
            $user->increment($balance_field,$params['addValue']);
        }
        return $this->success([]);
    }
}
