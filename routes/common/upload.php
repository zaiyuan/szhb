<?php
use Illuminate\Support\Facades\Route;

Route::post('uploadImage',[\App\Http\Controllers\UploadController::class,'upload']);

