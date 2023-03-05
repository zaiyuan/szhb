<?php

namespace App\Http\Controllers\api;

use App\Helpers\pageHelper;
use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\RechargeRecord;
use App\Models\WithdrawRecord;
use App\Services\HuobiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    /**
     * 我的资产
     * User: qiaohao
     * Date: 2023/2/26 15:37
     */
    public function myAsset()
    {
        $user=Auth::user();
        $user->append(['currency_list']);

        $list=$user['currency_list'];

        $btcusdt_ticker=HuobiService::btcusdtTicker();
        $ethusdt_ticker=HuobiService::ethusdtTicker();
        $aicusdt_ticker=Option::getCacheOptionByName('aicusdt');
        foreach($list as &$row){
            $row['total']=$row['balance']+$row['frozen'];
            if($row['currency_type']=='BTC'){
                $row['convert_usdt']=round($row['total']/$btcusdt_ticker,4);
            }else  if($row['currency_type']=='ETH'){
                $row['convert_usdt']=round($row['total']/$ethusdt_ticker,4);
            }else  if($row['currency_type']=='AIC'){
                $row['convert_usdt']=round($row['total']/$aicusdt_ticker,4);
            }else  if($row['currency_type']=='USDT'){
                $row['convert_usdt']=$row['total'];
            }
        }
        return $this->success([
            'list'=>$list
        ]);
    }

    /**
     * 充值记录
     * User: qiaohao
     * Date: 2023/2/26 15:52
     */
    public function rechargeRecord(Request $request)
    {
        $params=$request->input();
        $pageParam=pageHelper::initPageParam($params['page'],$params['pageSize']);
        $where=[];
        $user=Auth::user();
        $where[]=['user_id','=',$user['id']];
        if(isset($params['sdate']) && $params['sdate']){
            $where[]=[function($query)use($params){
                $query->whereBetween('created_at',[strtotime($params['sdate']),strtotime($params['edate'])+86399]);
            }];
        }
        if(isset($params['currency_type']) && $params['currency_type']){
            $where[]=['currency_type','=',strtolower($params['currency_type'])];
        }

        $count=RechargeRecord::where($where)->count();
        $list=RechargeRecord::where($where)
            ->select('*')
            ->orderBy('id','desc')
            ->offset($pageParam['offset'])
            ->limit($pageParam['pageSize'])
            ->get()
            ->toArray();
        $res= [
            'list'=>$list,
            'pagination'=>pageHelper::getPagination($params['page'],$params['pageSize'],$count)
        ];
        return $this->success($res);
    }

    /**
     * 提现记录
     * User: qiaohao
     * Date: 2023/2/26 16:24
     */
    public function withdrawRecord(Request $request)
    {
        $params=$request->input();
        $pageParam=pageHelper::initPageParam($params['page'],$params['pageSize']);
        $where=[];
        $user=Auth::user();
        $where[]=['user_id','=',$user['id']];
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
            ->get()
            ->toArray();
        $res= [
            'list'=>$list,
            'pagination'=>pageHelper::getPagination($params['page'],$params['pageSize'],$count)
        ];
        return $this->success($res);
    }
}
