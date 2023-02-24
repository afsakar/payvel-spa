<?php

use App\Http\Controllers\Api\AccountTypeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/account-types/restore/{id}', [AccountTypeController::class, 'restore'])->name('account_types.restore');
    Route::delete('/account-types/force-delete/{id}', [AccountTypeController::class, 'forceDelete'])->name('account_types.force-delete');
    Route::get('/account-types/trash', [AccountTypeController::class, 'trash'])->name('account_types.trash');
    Route::apiResource('account-types', AccountTypeController::class, array('as' => 'api'));
});
