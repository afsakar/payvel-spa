<?php

use App\Http\Controllers\Api\CurrencyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/currencies/restore/{id}', [CurrencyController::class, 'restore'])->name('currencies.restore');
    Route::delete('/currencies/force-delete/{id}', [CurrencyController::class, 'forceDelete'])->name('currencies.force-delete');
    Route::apiResource('currencies', CurrencyController::class, array('as' => 'api'));
});
