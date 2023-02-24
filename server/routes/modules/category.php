<?php

use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/categories/restore/{id}', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('/categories/force-delete/{id}', [CategoryController::class, 'forceDelete'])->name('categories.force-delete');
    Route::get('/categories/trash', [CategoryController::class, 'trash'])->name('categories.trash');
    Route::apiResource('categories', CategoryController::class, array('as' => 'api'));
});
