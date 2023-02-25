<?php

namespace App\Models;

use App\Tools\upload\UploadLib;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    use HasFactory;
    protected $table="carousel";
    protected $guarded=['r'];
    protected $casts=[
        'created_at'=>'date:Y-m-d H:i:s'
    ];
    protected static function booted()
    {
        static::deleted(function($model){
            if($model->image){
                $uploadLib=UploadLib::getUploadInstance();
                $uploadLib->delFile($model->image);
            }
        });
    }

    public function getImageAttribute($value)
    {
        $uploadlib=UploadLib::getUploadInstance();
        return $uploadlib->fullImage($value);
    }
}
