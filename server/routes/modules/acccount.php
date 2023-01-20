<?php

use App\Http\Controllers\Api\AccountController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/accounts/restore/{id}', [AccountController::class, 'restore'])->name('accounts.restore');
    Route::delete('/accounts/force-delete/{id}', [AccountController::class, 'forceDelete'])->name('accounts.force-delete');
    Route::apiResource('accounts', AccountController::class, array('as' => 'api'));
});
