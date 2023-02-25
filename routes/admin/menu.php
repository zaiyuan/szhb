<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['admin_auth'])->group(function(){
    //菜单
    Route::get('getMenuTree',[\App\Http\Controllers\admin\MenuController::class,'menuTree']);
    Route::post('addMenu',[\App\Http\Controllers\admin\MenuController::class,'menuAdd']);
    Route::get('menuDetail/{id}',[\App\Http\Controllers\admin\MenuController::class,'menuDetail']);
    Route::post('updateMenu',[\App\Http\Controllers\admin\MenuController::class,'menuUpdate']);
    Route::delete('delMenu/{id}',[\App\Http\Controllers\admin\MenuController::class,'menuDel']);
    Route::post('menuSetSort/{id}/{sort}',[\App\Http\Controllers\admin\MenuController::class,'set_sort']);
    Route::get('topMenu',[\App\Http\Controllers\admin\MenuController::class,'top_menu']);
    Route::get('currentMenu',[\App\Http\Controllers\admin\MenuController::class,'current_menu']);
});
