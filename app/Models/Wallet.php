<?php

namespace App\Models;

use App\Tools\upload\UploadLib;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $table="wallet";
    protected $dateFormat="U";
    protected $casts=[
        'created_at'=>'datetime:Y-m-d H:i:s',
        'updated_at'=>'datetime:Y-m-d H:i:s',
    ];
    protected $guarded=[];
    protected $appends=['btc_qrcode','eth_qrcode','usdt_qrcode','aic_qrcode'];
    const SYSTEM_CURRENCY_TYPE="aic";
    public static function getCurrencyTypes()
    {
        return ['btc','eth','usdt','aic'];
    }
    /**
     * btc_qrcode
     * @return string
     * User: qiaohao
     * Date: 2023/2/22 10:18
     */
    public function getBtcQrcodeAttribute()
    {
        $filename="wallet_qrcode/{$this->id}_btc.png";
        $qrcode=public_path($filename);
        $uploadLib=UploadLib::getUploadInstance();
        if(!file_exists($qrcode)){
            $img=new \QRcode();
            $img->png($this->btc_address,$filename,QR_ECLEVEL_L,5,2,false);
        }
        return $uploadLib->fullImage($filename);
    }

    /**
     * eth_qrcode
     * @return string
     * User: qiaohao
     * Date: 2023/2/22 10:20
     */
    public function getEthQrcodeAttribute()
    {
        $filename="wallet_qrcode/{$this->id}_eth.png";
        $qrcode=public_path($filename);
        $uploadLib=UploadLib::getUploadInstance();
        if(!file_exists($qrcode)){
            $img=new \QRcode();
            $img->png($this->eth_address,$filename,QR_ECLEVEL_L,5,2,false);
        }
        return $uploadLib->fullImage($filename);
    }

    /**
     * usdt_qrcode
     * @return string
     * User: qiaohao
     * Date: 2023/2/22 10:21
     */
    public function getUsdtQrcodeAttribute()
    {
        $filename="wallet_qrcode/{$this->id}_usdt.png";
        $qrcode=public_path($filename);
        $uploadLib=UploadLib::getUploadInstance();
        if(!file_exists($qrcode)){
            $img=new \QRcode();
            $img->png($this->usdt_address,$filename,QR_ECLEVEL_L,5,2,false);
        }
        return $uploadLib->fullImage($filename);
    }

    /**
     * aic_qrcode
     * @return string
     * User: qiaohao
     * Date: 2023/2/22 10:22
     */
    public function getAicQrcodeAttribute()
    {
        $filename="wallet_qrcode/{$this->id}_aic.png";
        $qrcode=public_path($filename);
        $uploadLib=UploadLib::getUploadInstance();
        if(!file_exists($qrcode)){
            $img=new \QRcode();
            $img->png($this->aic_address,$filename,QR_ECLEVEL_L,5,2,false);
        }
        return $uploadLib->fullImage($filename);
    }

    //随机返回id
    public static function getRandomWallet()
    {
        return self::inRandomOrder()->first();
    }
}
