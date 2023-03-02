<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\ExchangeRequest;
use App\Http\Requests\api\RechargeRequest;
use App\Http\Requests\api\WithdrawRequest;
use App\Models\ExchangeRecord;
use App\Models\RechargeRecord;
use App\Models\Wallet;
use App\Models\WithdrawRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 钱包管理
 * App\Http\Controllers\api
 * @author: qiaohao
 * @Time: 2023/2/24 13:46
 */
class WalletController extends Controller
{
    /**
     * 充值
     * User: qiaohao
     * Date: 2023/2/24 13:47
     */
    public function recharge(RechargeRequest $request)
    {
        $params=$request->input();
        $user=Auth::user();
        $wallet=Wallet::find($user['wallet_id']);
        if(!$wallet){
            throw new \Exception("钱包不存在");
        }
        RechargeRecord::create([
            'user_id'=>$user['id'],
            'currency_type'=>$params['currency_type'],
            'currency_address'=>$wallet[$params['currency_type'].'_address'],
            'money'=>$params['money'],
            'status'=>RechargeRecord::STATUS_AUDITING
        ]);
        return $this->success([]);
    }

    /**
     * 提现
     * User: qiaohao
     * Date: 2023/2/24 15:36
     */
    public function withdraw(WithdrawRequest $request)
    {
        $params=$request->input();
        $user=Auth::user();
        $wallet=Wallet::find($user['wallet_id']);
        if(!$wallet){
            throw new \Exception("钱包不存在");
        }
        $balance_key=$params['currency_type']."_balance";
        if($user[$balance_key]<$params['money']){
            throw new \Exception("余额不足");
        }
        DB::beginTransaction();
        try{
            $user->$balance_key-=$params['money'];
            $user->save();

            WithdrawRecord::create([
                'user_id'=>$user['id'],
                'currency_type'=>$params['currency_type'],
                'currency_address'=>$wallet[$params['currency_type'].'_address'],
                'money'=>$params['money'],
                'status'=>WithdrawRecord::STATUS_AUDITING
            ]);
            DB::commit();
            return $this->success([]);
        }catch (\Exception $e){
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 兑换
     * User: qiaohao
     * Date: 2023/2/26 18:13
     */
    public function exchange(ExchangeRequest $request)
    {
        $params=$request->input();
        $user=Auth::user();
        $balance_key=$params['from_currency_type']."_balance";
        if($user[$balance_key]<$params['money']){
            throw new \Exception("余额不足");
        }
        DB::beginTransaction();
        try{
            ExchangeRecord::create([
                'user_id'=>$user['id'],
                'from_currency_type'=>$params['from_currency_type'],
                'to_currency_type'=>Wallet::SYSTEM_CURRENCY_TYPE,
                'money'=>$params['money'],
                'status'=>ExchangeRecord::STATUS_AUDITING
            ]);
            DB::commit();
            return $this->success([]);
        }catch (\Exception $e){
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
