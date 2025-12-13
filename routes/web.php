<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('accounts')->name('accounts.')->group(function () {
    Route::post('/', [AccountController::class, 'store'])->name('store');
    Route::put('/{account}', [AccountController::class, 'update'])->name('update');
    Route::post('/{account}/close', [AccountController::class, 'close'])->name('close');
    Route::get('/{account}/aggregate-balance', [AccountController::class, 'aggregateBalance'])->name('aggregate');
});
