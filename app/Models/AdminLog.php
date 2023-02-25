<?php

namespace App\Models;

use App\Models\system\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    use HasFactory;

    protected $table="system_admin_log";
    public $timestamps=false;
    protected $guarded=[];
    public static function addOne($data)
    {
        AdminLog::create($data);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class,'admin_id','id');
    }
}
