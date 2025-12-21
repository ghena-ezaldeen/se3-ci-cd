<?php


use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountStateController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\TransactionReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/




Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
//Route::middleware('auth:sanctum')->post('/createFcmToken', [AuthController::class, 'createFcmToken']);



Route::post('/reports/audit-logs', [TransactionReportController::class, 'auditLogs'])
    ->middleware('auth:sanctum')
    ->name('reports.audit-logs');


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reports/transaction_reports', [TransactionReportController::class, 'transactions']);
});

Route::middleware('auth:sanctum')
    ->prefix('accounts')
    ->group(function () {

        Route::post('/', [AccountController::class, 'store'])->name('store');
        Route::post('/{account}', [AccountController::class, 'update'])->name('update');
        Route::post('/{account}/close', [AccountController::class, 'close'])->name('close');
        Route::get('/{account}/aggregate-balance', [AccountController::class, 'aggregateBalance'])->name('aggregate');
        Route::post('{account}/state', [AccountStateController::class, 'change'])->name('accounts.changeState');
    });
