<?php

use App\Http\Controllers\Api\MaterialController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/materials/restore/{id}', [MaterialController::class, 'restore'])->name('materials.restore');
    Route::delete('/materials/force-delete/{id}', [MaterialController::class, 'forceDelete'])->name('materials.force-delete');
    Route::get('/materials/trash', [MaterialController::class, 'trash'])->name('materials.trash');
    Route::apiResource('materials', MaterialController::class, array('as' => 'api'));
});
