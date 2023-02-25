<?php

namespace App\Services;

use App\Helpers\pageHelper;
use App\Models\Carousel;
use App\Models\Wallet;

class WalletService
{
    /**
     * 钱包列表（分页）
     * @param $params
     * @return array
     * User: qiaohao
     * Date: 2023/2/21 21:09
     */
    public function getList($params)
    {
        $pageParam=pageHelper::initPageParam($params['page'],$params['pageSize']);
        $where=[];
        $count=Wallet::where($where)->count();
        $list=Wallet::where($where)
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

    /**
     * 钱包新增
     * @param $params
     * User: qiaohao
     * Date: 2023/2/21 21:09
     */
    public function add($params)
    {
        Wallet::create($params);
    }

    /**
     * 编辑
     * @param $params
     * User: qiaohao
     * Date: 2023/2/21 21:13
     */
    public function modify($params)
    {
        $wallet=Wallet::find($params['id']);
        $wallet->fill($params);
        $wallet->save();
    }

    /**
     * 删除
     * @param $id
     * User: qiaohao
     * Date: 2023/2/21 21:16
     */
    public function delete($id)
    {
//        $wallet=Wallet::find($id)->toArray();
        Wallet::destroy($id);
    }
}
