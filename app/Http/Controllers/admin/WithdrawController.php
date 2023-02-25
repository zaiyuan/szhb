<?php

namespace App\Http\Controllers\admin;

use App\Helpers\pageHelper;
use App\Http\Controllers\Controller;
use App\Models\RechargeRecord;
use App\Models\User;
use App\Models\WithdrawRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    /**
     * 提现记录
     * User: qiaohao
     * Date: 2023/2/24 14:21
     */
    public function index(Request $request)
    {
        $params=$request->input();
        $pageParam=pageHelper::initPageParam($params['page'],$params['pageSize']);
        $where=[];
        if(isset($params['user_id']) && $params['user_id']){
            $where[]=['user_id','=',$params['user_id']];
        }
        if(isset($params['status']) && $params['status']){
            $where[]=['status','=',$params['status']];
        }
        if(isset($params['sdate']) && $params['sdate']){
            $where[]=[function($query)use($params){
                $query->whereBetween('created_at',[strtotime($params['sdate']),strtotime($params['edate'])+86399]);
            }];
        }

        $count=WithdrawRecord::where($where)->count();
        $list=WithdrawRecord::where($where)
            ->select('*')
            ->orderBy('id','desc')
            ->offset($pageParam['offset'])
            ->limit($pageParam['pageSize'])
            ->with([
                'user:id,mobile,email'
            ])
            ->get()
            ->toArray();
        $res= [
            'list'=>$list,
            'pagination'=>pageHelper::getPagination($params['page'],$params['pageSize'],$count)
        ];
        return $this->success($res);
    }

    /**
     * 充值记录审核
     * User: qiaohao
     * Date: 2023/2/24 14:25
     */
    public function audit(Request $request)
    {
        $id=$request->input('id');
        $status=$request->input('status');
        $model=WithdrawRecord::find($id);
        if($model['status']!=RechargeRecord::STATUS_AUDITING){
            throw new \Exception("状态错误");
        }

        DB::beginTransaction();
        try{
            $model->status=$status;
            $model->audit_time=time();
            $model->save();

            if($status==3){//拒绝，金额返回用户钱包
                $key=$model['currency_type'].'_balance';
                $user=User::find($model['user_id']);
                $user->$key+=$model['money'];
                $user->save();
            }
            DB::commit();
            return $this->success();
        }catch(\Exception $e){
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
