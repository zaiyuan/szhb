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

Route::get('/carousel',[\App\Http\Controllers\api\HomeController::class,'carousel']);
Route::get('/announcement',[\App\Http\Controllers\api\HomeController::class,'announcement']);
Route::get('/announcementDetail/{id}',[\App\Http\Controllers\api\HomeController::class,'announcementDetail']);
Route::get('/instruction',[\App\Http\Controllers\api\HomeController::class,'instruction']);
Route::get('/instructionDetail/{id}',[\App\Http\Controllers\api\HomeController::class,'instructionDetail']);
Route::post('/getConfig',[\App\Http\Controllers\api\HomeController::class,'getConfig']);

Route::middleware(['auth'])->group(function(){
});

