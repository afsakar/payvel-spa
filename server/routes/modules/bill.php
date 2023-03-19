<?php

use App\Http\Controllers\Api\BillController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/bills/restore/{id}', [BillController::class, 'restore'])->name('bills.restore');
    Route::delete('/bills/force-delete/{id}', [BillController::class, 'forceDelete'])->name('bills.force-delete');
    Route::get('/bills/{company}', [BillController::class, 'index'])->name('bills.index');
    Route::get('/bills/get-bill/{id}', [BillController::class, 'show'])->name('bills.getBill');
    Route::get('/bills/{company}/trash', [BillController::class, 'trash'])->name('bills.trash');
    Route::post('/bills/{id}/items', [BillController::class, 'saveBillItems'])->name('bills.save-items');
    Route::apiResource('bills', BillController::class, array('as' => 'api'));
})->scopeBindings();
