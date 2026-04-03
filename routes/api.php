<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::post('/invoices', [InvoiceController::class, 'store']);
Route::get('/invoices', [InvoiceController::class, 'index']);
Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
Route::put('/invoices/{id}/status', [InvoiceController::class, 'updateStatus']);
Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy']);
Route::get('/dashboard', [InvoiceController::class, 'dashboard']);
Route::get('/invoices/{id}/pdf', [InvoiceController::class, 'downloadPdf']);
