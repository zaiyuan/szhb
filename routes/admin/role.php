<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['admin_auth'])->group(function(){
    //角色
    Route::post('getRoleList',[\App\Http\Controllers\admin\RoleController::class,'index']);//角色列表，分页
    Route::get('getAllRole',[\App\Http\Controllers\admin\RoleController::class,'all']);//所有角色，不分页
    Route::post('addRole',[\App\Http\Controllers\admin\RoleController::class,'add']);//角色新增
    Route::post('updateRole',[\App\Http\Controllers\admin\RoleController::class,'update']);//角色编辑
    Route::delete('delRole/{id}',[\App\Http\Controllers\admin\RoleController::class,'delete']);//角色删除
    Route::get('roleDetail/{id}',[\App\Http\Controllers\admin\RoleController::class,'detail']);//角色详情
    Route::get('roleSetStatus/{id}',[\App\Http\Controllers\admin\RoleController::class,'set_status']);
});
