<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['admin_auth'])->group(function(){
    Route::post('/announcementList',[\App\Http\Controllers\admin\AnnouncementController::class,'index']);
    Route::post('/announcementAdd',[\App\Http\Controllers\admin\AnnouncementController::class,'add']);
    Route::post('/announcementUpdate',[\App\Http\Controllers\admin\AnnouncementController::class,'modify']);
    Route::get('/announcementDetail/{id}',[\App\Http\Controllers\admin\AnnouncementController::class,'detail']);
    Route::get('/announcementSetStatus/{id}',[\App\Http\Controllers\admin\AnnouncementController::class,'set_is_online']);
    Route::delete('/announcementDelete/{id}',[\App\Http\Controllers\admin\AnnouncementController::class,'delete']);
    Route::post('/announcementSetSort',[\App\Http\Controllers\admin\AnnouncementController::class,'sort']);
});
