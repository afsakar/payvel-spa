<?php

use App\Http\Controllers\Api\AgreementController;
use App\Http\Controllers\Api\AgreementMediaController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/agreements/restore/{id}', [AgreementController::class, 'restore'])->name('agreements.restore');
    Route::delete('/agreements/force-delete/{id}', [AgreementController::class, 'forceDelete'])->name('agreements.force-delete');
    Route::get('/agreements/{company}', [AgreementController::class, 'index'])->name('agreements.index');
    Route::get('/agreements/get-agreement/{id}', [AgreementController::class, 'show'])->name('agreements.getAgreement');
    Route::apiResource('agreements.media', AgreementMediaController::class, array('as' => 'api'));
    Route::get('/agreements/{company}/trash', [AgreementController::class, 'trash'])->name('agreements.trash');
    Route::apiResource('agreements', AgreementController::class, array('as' => 'api'));
})->scopeBindings();
