<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRecord extends Model
{
    use HasFactory;

    protected $table="exchange_record";
    protected $dateFormat="U";
    protected $casts=[
        'created_at'=>'datetime:Y-m-d H:i:s',
        'updated_at'=>'datetime:Y-m-d H:i:s',
        'audit_time'=>'datetime:Y-m-d H:i:s',
    ];
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat?:'Y-m-d H:i:s');
    }
    const STATUS_AUDITING=1;
    const STATUS_YES=2;
    const STATUS_NO=3;
    protected $guarded=[];
    protected $appends=['status_text'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    /**
     * 状态描述 status_text
     * @return string
     * User: qiaohao
     * Date: 2023/2/24 14:33
     */
    public function getStatusTextAttribute()
    {
        $arr=[
            self::STATUS_AUDITING=>'待审核',
            self::STATUS_YES=>'已确认',
            self::STATUS_NO=>'已拒绝',
        ];
        return isset($arr[$this->status])?$arr[$this->status]:"";
    }
}
