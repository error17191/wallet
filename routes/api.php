<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::get('accounts', [AccountController::class, 'index']);
Route::get('accounts/{id}', [AccountController::class, 'show']);
Route::post('accounts', [AccountController::class, 'create']);
Route::post('accounts/{id}/top-up', [AccountController::class, 'topUp']);
Route::post('accounts/{id}/charge', [AccountController::class, 'charge']);
