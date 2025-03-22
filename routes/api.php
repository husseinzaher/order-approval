<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ApprovalController;

Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{order}', [OrderController::class, 'show']);
Route::get('/orders/{order}/history', [OrderController::class, 'history']);
Route::post('/orders/{order}/approve', [ApprovalController::class, 'approve']);
