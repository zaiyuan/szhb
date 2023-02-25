<?php

namespace App\Models\system;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemMenu extends Model
{
    use HasFactory;
    protected $table="system_menu";
    public $timestamps=false;
    protected $guarded=[];
}
