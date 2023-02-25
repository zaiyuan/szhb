<?php

namespace App\Models\system;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemRole extends Model
{
    use HasFactory;
    protected $table="system_role";
    protected $fillable=['name','desc','act_list'];

    //是否是超级管理员  is_super
    public function getIsSuperAttribute()
    {
        return $this->id==1?1:0;
    }

    //管理员
    public function admins()
    {
        return $this->hasMany(Admin::class,'role_id','id');
    }

    //能否删除：1能 0不能 can_delete
    public function getCanDeleteAttribute()
    {
        return $this->id==1?0:1;
    }
}
