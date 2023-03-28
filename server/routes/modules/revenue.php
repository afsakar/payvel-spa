<?php

use App\Http\Controllers\Api\RevenueController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/revenues/restore/{id}', [RevenueController::class, 'restore'])->name('revenues.restore');
    Route::delete('/revenues/force-delete/{id}', [RevenueController::class, 'forceDelete'])->name('revenues.force-delete');
    Route::get('/revenues/{company}', [RevenueController::class, 'index'])->name('revenues.index');
    Route::get('/revenues/get-revenue/{id}', [RevenueController::class, 'show'])->name('revenues.getRevenue');
    Route::get('/revenues/{company}/trash', [RevenueController::class, 'trash'])->name('revenues.trash');
    Route::apiResource('revenues', RevenueController::class, array('as' => 'api'));
})->scopeBindings();
