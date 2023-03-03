<?php

namespace App\Services;

use App\Helpers\HttpRequest;
use Illuminate\Support\Facades\Cache;

class HuobiService
{
    private $host="https://api.huobi.pro/";

    public function tradePair()
    {
        return [
            'btcusdt',
            'ethusdt',
            'xrpusdt',
            'dogeusdt',
            'ltcusdt',
            'trxusdt',
            'shibusdt',
            'filusdt',
            'stgusdt',
        ];
    }
    /**
     * 所有交易对的最新 Tickers
     * User: qiaohao
     * Date: 2023/3/2 19:39
     */
    public function market_tickers()
    {
        $action="market/tickers";
        $url=$this->host.$action;
        $httpRequest=new HttpRequest();
        $httpRequest->url=$url;
        $res=$httpRequest->send();
        $res= json_decode($res,true);

        $tradePair=$this->tradePair();
        $data=[];
        foreach($res['data'] as $row){
            if(in_array($row['symbol'],$tradePair)){
                $row['change']=round(($row['close']-$row['open'])/$row['open'],4)*100;
                $row['change']= $row['change']<=0? $row['change']:'+'. $row['change'];
                $row['change'].='%';
                $data[]=$row;
            }
        }
        return $data;
    }

    public function getMarketTickersCache()
    {
        $data=Cache::get('market_tickers');
        if($data){
            return json_decode($data,true);
        }
        $data=$this->market_tickers();
        Cache::put('market_tickers',json_encode($data),now()->addMinutes(1));
        return $data;
    }

    public function kData($symbol,$period,$size=150)
    {
        $action="market/tickers";
        $url=$this->host.$action."?symbol={$symbol}&period={$period}&size={$size}";
        $httpRequest=new HttpRequest();
        $httpRequest->url=$url;
        $res=$httpRequest->send();
        $res= json_decode($res,true);
        return $res['data'];
    }

    public function getKDataCache($symbol,$period)
    {
        $key="kdata_".$symbol.'_'.$period;
        $data=Cache::get($key);
        if($data){
            return json_decode($data,true);
        }
        $data=$this->kData($symbol,$period);
        Cache::put($key,json_encode($data),now()->addMinutes(1));
        return $data;

        $res=$this->kData($symbol,$period);
    }
}
