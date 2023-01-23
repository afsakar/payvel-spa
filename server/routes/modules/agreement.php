<?php

use App\Http\Controllers\Api\AgreementController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/agreements/restore/{id}', [AgreementController::class, 'restore'])->name('agreements.restore');
    Route::delete('/agreements/force-delete/{id}', [AgreementController::class, 'forceDelete'])->name('agreements.force-delete');
    Route::apiResource('agreements', AgreementController::class, array('as' => 'api'));
});
