<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['admin_auth'])->group(function(){
    Route::post('/getConfig',[\App\Http\Controllers\admin\OptionController::class,'getConfig']);
    Route::post('/saveConfig',[\App\Http\Controllers\admin\OptionController::class,'saveConfig']);
});
