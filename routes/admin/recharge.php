<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['admin_auth'])->group(function(){
    Route::post('/rechargeList',[\App\Http\Controllers\admin\RechargeController::class,'index']);
    Route::post('/rechargeAudit',[\App\Http\Controllers\admin\RechargeController::class,'audit']);
});
