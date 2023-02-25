<?php
use Illuminate\Support\Facades\Route;
Route::post('/clearCache',[\App\Http\Controllers\admin\UserController::class,'clearCache']);

Route::middleware(['admin_auth'])->group(function(){
    Route::post('/userList',[\App\Http\Controllers\admin\UserController::class,'index']);
    Route::get('/userDetail/{id}',[\App\Http\Controllers\admin\UserController::class,'detail']);
    Route::delete('/userDelele/{id}',[\App\Http\Controllers\admin\UserController::class,'delete']);
    Route::get('/useStatus/{id}',[\App\Http\Controllers\admin\UserController::class,'setStatus']);
    Route::post('/usePasswordEdit',[\App\Http\Controllers\admin\UserController::class,'passwordEdit']);
    Route::post('/useAddCurrency',[\App\Http\Controllers\admin\UserController::class,'addCurrency']);
    Route::post('/useFreeze',[\App\Http\Controllers\admin\UserController::class,'freeze']);
});
