<?php

use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->get('/summary', [DashboardController::class, 'index'])->name('api.summary');

Route::post('/midtrans/webhook', [\App\Http\Controllers\MidtransController::class, 'webhook'])->name('api.midtrans.webhook');
