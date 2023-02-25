<?php

use App\Http\Controllers\Api\WaybillController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/waybills/restore/{id}', [WaybillController::class, 'restore'])->name('waybills.restore');
    Route::delete('/waybills/force-delete/{id}', [WaybillController::class, 'forceDelete'])->name('waybills.force-delete');
    Route::get('/waybills/{company}', [WaybillController::class, 'index'])->name('waybills.index');
    Route::get('/waybills/get-waybill/{id}', [WaybillController::class, 'show'])->name('waybills.getWaybill');
    Route::get('/waybills/{company}/trash', [WaybillController::class, 'trash'])->name('waybills.trash');
    Route::post('/waybills/{id}/items', [WaybillController::class, 'saveWaybillItems'])->name('waybills.save-items');
    Route::apiResource('waybills', WaybillController::class, array('as' => 'api'));
})->scopeBindings();
