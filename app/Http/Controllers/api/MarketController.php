<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\HuobiService;
use Illuminate\Http\Request;

/**
 * 行情数据
 * App\Http\Controllers\api
 * @author: qiaohao
 * @Time: 2023/3/2 19:17
 */
class MarketController extends Controller
{
    /**
     * 最新数据对
     * @return \Illuminate\Http\JsonResponse
     * User: qiaohao
     * Date: 2023/3/2 19:58
     */
    public function market_tickers()
    {
        $service=new HuobiService();
        $res=$service->getMarketTickersCache();
        return $this->success($res);
    }

    public function k_data(Request $request)
    {
        $symbol=$request->input('symbol');
        $period=$request->input('period');
//        $size=$request->input('size');
        $service=new HuobiService();
        $res=$service->getKDataCache($symbol,$period);
        return $this->success($res);
    }
}
