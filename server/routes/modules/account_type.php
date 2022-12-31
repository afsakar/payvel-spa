<?php

use App\Http\Controllers\Api\AccountTypeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/account-types/restore/{id}', [AccountTypeController::class, 'restore'])->name('account_types.restore');
    Route::delete('/account-types/force-delete/{id}', [AccountTypeController::class, 'forceDelete'])->name('account_types.force-delete');
    Route::apiResource('account-types', AccountTypeController::class, array('as' => 'api'));
});
