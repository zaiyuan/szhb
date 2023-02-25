<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/login',[\App\Http\Controllers\admin\AdminController::class,'login']);

Route::middleware(['admin_auth'])->group(function(){
    Route::post('/getAdminList',[\App\Http\Controllers\admin\AdminController::class,'index']);//管理员列表
    Route::post('/getAdminAll',[\App\Http\Controllers\admin\AdminController::class,'all']);//管理员select数据
    Route::post('/addAdmin',[\App\Http\Controllers\admin\AdminController::class,'add']);//管理员新增
    Route::post('/updateAdmin',[\App\Http\Controllers\admin\AdminController::class,'update']);//管理员编辑
    Route::delete('/delAdmin/{id}',[\App\Http\Controllers\admin\AdminController::class,'delete']);//删除管理员
    Route::get('/adminDetail/{id}',[\App\Http\Controllers\admin\AdminController::class,'detail']);//删除管理员
    Route::post('/modify_pwd',[\App\Http\Controllers\admin\AdminController::class,'password_edit']);//当前登陆管理员修改密码
    Route::get('/adminSetStatus/{id}',[\App\Http\Controllers\admin\AdminController::class,'set_status']);
    Route::get('/adminTest',[\App\Http\Controllers\admin\AdminController::class,'test']);
    Route::post('/adminLog',[\App\Http\Controllers\admin\AdminController::class,'admin_log']);
});

