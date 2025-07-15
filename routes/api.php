<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\WalletController;
use App\Http\Middleware\FakeAdminMiddleware;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransferController;

Route::post('register', [AuthenticationController::class, 'register']);
Route::post('login', [AuthenticationController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthenticationController::class, 'logOut']);

    Route::post('/transfer', [TransferController::class, 'transfer']);
    Route::post('/refund', [TransferController::class, 'refund']);
    Route::post('/withdraw', [WalletController::class, 'withdraw']);
    Route::post('/deposit', [WalletController::class, 'deposit']);
});

Route::middleware(FakeAdminMiddleware::class)->group(function () {
    Route::get('/admin/users', [UserController::class, 'index']);
    Route::get('/admin/transfers', [TransferController::class, 'index']);
});
