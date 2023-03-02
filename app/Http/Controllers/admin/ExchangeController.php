<?php

namespace App\Http\Controllers\admin;

use App\Helpers\pageHelper;
use App\Http\Controllers\Controller;
use App\Models\ExchangeRecord;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    /**
     * 兑换记录
     * User: qiaohao
     * Date: 2023/2/26 16:52
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

        $count=ExchangeRecord::where($where)->count();
        $list=ExchangeRecord::where($where)
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

    public function audit(Request $request)
    {
        $id=$request->input('id');
        $status=$request->input('status');
        $model=ExchangeRecord::find($id);
        if($model['status']!=ExchangeRecord::STATUS_AUDITING){
            throw new \Exception("状态错误");
        }
        $model->status=$status;
        $model->audit_time=time();
        $model->save();
        return $this->success();
    }
}
