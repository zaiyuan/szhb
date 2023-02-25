<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['admin_auth'])->group(function(){
    Route::post('/walletList',[\App\Http\Controllers\admin\WalletController::class,'index']);
    Route::post('/walletAdd',[\App\Http\Controllers\admin\WalletController::class,'add']);
    Route::post('/walletUpdate',[\App\Http\Controllers\admin\WalletController::class,'modify']);
    Route::delete('/walletDelete/{id}',[\App\Http\Controllers\admin\WalletController::class,'delete']);
});
