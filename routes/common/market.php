<?php
use Illuminate\Support\Facades\Route;

Route::get('marketTicket',[\App\Http\Controllers\api\MarketController::class,'market_tickers']);
Route::post('kData',[\App\Http\Controllers\api\MarketController::class,'k_data']);


