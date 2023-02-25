<?php
use Illuminate\Support\Facades\Route;

Route::get('checkSignature',[\App\Http\Controllers\UtilController::class,'checkSignature']);//验证服务器


