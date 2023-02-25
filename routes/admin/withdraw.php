<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['admin_auth'])->group(function(){
    Route::post('/withdrawList',[\App\Http\Controllers\admin\WithdrawController::class,'index']);
    Route::post('/withdrawAudit',[\App\Http\Controllers\admin\WithdrawController::class,'audit']);
});
