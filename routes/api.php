<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountStateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {


    Route::prefix('accounts')->group(function () {
        Route::post('/', [AccountController::class, 'store'])->name('store');
        Route::post('/{account}', [AccountController::class, 'update'])->name('update');
        Route::post('/{account}/close', [AccountController::class, 'close'])->name('close');
        Route::get('/{account}/aggregate-balance', [AccountController::class, 'aggregateBalance'])->name('aggregate');
        Route::post('{account}/state', [AccountStateController::class, 'change'])->name('accounts.changeState');
    });


    Route::post('/transfer', [TransactionController::class, 'store']);
    Route::post('/deposit', [TransactionController::class, 'deposit']);
    Route::post('/withdraw', [TransactionController::class, 'withdraw']);
});
