<?php

namespace App\Models\system;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;
    protected $table="system_admin";

    protected $casts=[
        'created_at'=>'datetime:Y-m-d H:i:s',
        'updated_at'=>'datetime:Y-m-d H:i:s',
        'last_login'=>'datetime:Y-m-d H:i:s',
    ];
    protected $hidden=['password'];

    protected $guarded=['password_confirmation'];

    //当前管理员是否是系统管理员
    public function isAdministrator()
    {
        return $this->id==1;
    }
    public function role()
    {
        return $this->belongsTo(SystemRole::class,'role_id','id');
    }

    //能否删除：1能 0不能 can_delete
    public function getCanDeleteAttribute()
    {
        return $this->id==1?0:1;
    }
}
