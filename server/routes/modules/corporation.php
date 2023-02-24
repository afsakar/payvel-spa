<?php

use App\Http\Controllers\Api\CorporationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/corporations/restore/{id}', [CorporationController::class, 'restore'])->name('corporations.restore');
    Route::delete('/corporations/force-delete/{id}', [CorporationController::class, 'forceDelete'])->name('corporations.force-delete');
    Route::get('/corporations/trash', [CorporationController::class, 'trash'])->name('corporations.trash');
    Route::apiResource('corporations', CorporationController::class, array('as' => 'api'));
});
