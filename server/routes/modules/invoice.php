<?php

use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/invoices/restore/{id}', [InvoiceController::class, 'restore'])->name('invoices.restore');
    Route::delete('/invoices/force-delete/{id}', [InvoiceController::class, 'forceDelete'])->name('invoices.force-delete');
    Route::get('/invoices/{company}', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/get-invoice/{id}', [InvoiceController::class, 'show'])->name('invoices.getInvoice');
    Route::get('/invoices/{company}/trash', [InvoiceController::class, 'trash'])->name('invoices.trash');
    Route::post('/invoices/{id}/items', [InvoiceController::class, 'saveInvoiceItems'])->name('invoices.save-items');
    Route::apiResource('invoices', InvoiceController::class, array('as' => 'api'));
})->scopeBindings();
