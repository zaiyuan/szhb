<?php
use Illuminate\Support\Facades\Route;
Route::middleware(['admin_auth'])->group(function(){
    Route::post('/instructionList',[\App\Http\Controllers\admin\InstructionController::class,'index']);
    Route::post('/instructionAdd',[\App\Http\Controllers\admin\InstructionController::class,'add']);
    Route::post('/instructionUpdate',[\App\Http\Controllers\admin\InstructionController::class,'modify']);
    Route::get('/instructionDetail/{id}',[\App\Http\Controllers\admin\InstructionController::class,'detail']);
    Route::get('/instructionSetStatus/{id}',[\App\Http\Controllers\admin\InstructionController::class,'set_is_online']);
    Route::delete('/instructionDelete/{id}',[\App\Http\Controllers\admin\InstructionController::class,'delete']);
    Route::post('/instructionSetSort',[\App\Http\Controllers\admin\InstructionController::class,'sort']);
});
