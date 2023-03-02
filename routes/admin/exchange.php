<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['admin_auth'])->group(function(){
    Route::post('/exchangeList',[\App\Http\Controllers\admin\ExchangeController::class,'index']);
    Route::post('/exchangeAudit',[\App\Http\Controllers\admin\ExchangeController::class,'audit']);
});
