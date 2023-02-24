<?php

use App\Http\Controllers\Api\CompanyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/companies/restore/{id}', [CompanyController::class, 'restore'])->name('companies.restore');
    Route::delete('/companies/force-delete/{id}', [CompanyController::class, 'forceDelete'])->name('companies.force-delete');
    Route::get('/companies/trash', [CompanyController::class, 'trash'])->name('companies.trash');
    Route::apiResource('companies', CompanyController::class, array('as' => 'api'));
});
