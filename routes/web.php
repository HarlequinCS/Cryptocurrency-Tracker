<?php
use App\Http\Controllers\CryptoController;

Route::get('/', [CryptoController::class, 'index']);
Route::get('/coins/{id}', [CryptoController::class, 'show']);
Route::get('/api/coins', [CryptoController::class, 'apiCoins']);
