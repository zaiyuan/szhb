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


Route::middleware(['auth'])->group(function(){
    Route::post('/recharge',[\App\Http\Controllers\api\WalletController::class,'recharge']);
    Route::post('/withdraw',[\App\Http\Controllers\api\WalletController::class,'withdraw']);
});

