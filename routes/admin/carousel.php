<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['admin_auth'])->group(function(){
    Route::post('/carouselList',[\App\Http\Controllers\admin\CarouselController::class,'index']);//轮播列表
    Route::post('/carouselAdd',[\App\Http\Controllers\admin\CarouselController::class,'add']);//轮播图新增
    Route::post('/carouselUpdate',[\App\Http\Controllers\admin\CarouselController::class,'update']);//轮播编辑
    Route::delete('/carouselDelete/{id}',[\App\Http\Controllers\admin\CarouselController::class,'delete']);//轮播删除
    Route::post('/carouselSetSort',[\App\Http\Controllers\admin\CarouselController::class,'set_sort']);//排序
    Route::post('/carouselSetIsOnline/{id}',[\App\Http\Controllers\admin\CarouselController::class,'set_is_online']);//排序
});
