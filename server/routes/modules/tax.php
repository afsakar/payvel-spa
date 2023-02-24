<?php

use App\Http\Controllers\Api\TaxController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/taxes/restore/{id}', [TaxController::class, 'restore'])->name('taxes.restore');
    Route::delete('/taxes/force-delete/{id}', [TaxController::class, 'forceDelete'])->name('taxes.force-delete');
    Route::get('/taxes/trash', [TaxController::class, 'trash'])->name('taxes.trash');
    Route::apiResource('taxes', TaxController::class, array('as' => 'api'));
});
