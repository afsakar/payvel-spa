<?php

use App\Http\Controllers\Api\UnitController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/units/restore/{id}', [UnitController::class, 'restore'])->name('units.restore');
    Route::delete('/units/force-delete/{id}', [UnitController::class, 'forceDelete'])->name('units.force-delete');
    Route::apiResource('units', UnitController::class, array('as' => 'api'));
});
