<?php

namespace App\Models;

use App\lib\StrLib;
use App\Services\HuobiService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $dateFormat='U';
    protected $casts = [
        'created_at'=>'datetime:Y-m-d H:i:s',
        'updated_at'=>'datetime:Y-m-d H:i:s',
    ];

    protected $appends=['account'];
    protected $guarded=[];
    //账号 account
    public function getAccountAttribute()
    {
        return $this->mobile?$this->mobile:$this->email;
    }

    /**
     * 币种余额列表  currency_list
     * @return array[]
     * User: qiaohao
     * Date: 2023/2/22 22:17
     */
    public function getCurrencyListAttribute()
    {
        return [
            ['currency_type'=>'BTC','balance'=>$this->btc_balance,'frozen'=>$this->btc_frozen],
            ['currency_type'=>'ETH','balance'=>$this->eth_balance,'frozen'=>$this->eth_frozen],
            ['currency_type'=>'USDT','balance'=>$this->usdt_balance,'frozen'=>$this->usdt_frozen],
            ['currency_type'=>'AIC','balance'=>$this->aic_balance,'frozen'=>$this->aic_frozen],
        ];
    }

    /**
     * 总资产,换算成usdt
     * User: qiaohao
     * Date: 2023/3/4 21:43
     */
    public function getTotalAssetAttribute()
    {
        $btc=$this->btc_balance+$this->btc_frozen;
        $eth=$this->eth_balance+$this->eth_balance;
        $usdt=$this->usdt_balance+$this->usdt_frozen;
        $aic=$this->aic_balance+$this->aic_frozen;

        $btcusdt_ticker=HuobiService::btcusdtTicker();
        $ethusdt_ticker=HuobiService::ethusdtTicker();
        $aicusdt_ticker=Option::getCacheOptionByName('aicusdt');
        return round($btc/$btcusdt_ticker+$eth/$ethusdt_ticker+$aic/$aicusdt_ticker+$usdt,4);
    }
}
