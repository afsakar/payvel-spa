<?php

use App\Http\Controllers\Api\WithholdingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/withholdings/restore/{id}', [WithholdingController::class, 'restore'])->name('withholdings.restore');
    Route::delete('/withholdings/force-delete/{id}', [WithholdingController::class, 'forceDelete'])->name('withholdings.force-delete');
    Route::get('/withholdings/trash', [WithholdingController::class, 'trash'])->name('withholdings.trash');
    Route::apiResource('withholdings', WithholdingController::class, array('as' => 'api'));
});
