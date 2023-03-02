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

Route::post('/register',[\App\Http\Controllers\api\SiteController::class,'register']);
Route::post('/login',[\App\Http\Controllers\api\SiteController::class,'login']);
Route::post('/fake_login',[\App\Http\Controllers\api\SiteController::class,'fake_login']);
Route::post('/findPassword',[\App\Http\Controllers\api\SiteController::class,'findPassword']);
Route::post('/sendCode',[\App\Http\Controllers\api\SiteController::class,'sendCode']);

Route::middleware(['auth'])->group(function(){
    Route::get('/userinfo',[\App\Http\Controllers\api\SiteController::class,'userinfo']);
    Route::post('/userinfo_modify',[\App\Http\Controllers\api\SiteController::class,'userinfo_modify']);
});

