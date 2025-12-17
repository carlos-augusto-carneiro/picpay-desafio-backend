<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/users', [UserController::class, 'store']);

Route::middleware(['auth:api'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refreshLogin']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/wallet/deposit', [WalletController::class,'deposit']);
    Route::post('/wallet/withdraw', [WalletController::class,'withdraw']);
    Route::post('/wallet/transfer', [WalletController::class,'transfer']);
    Route::get('/wallet/{id}/statement', [WalletController::class, 'statement']);
    Route::get('/wallet/{id}/balance', [WalletController::class, 'balance']);
    Route::get('/wallet/{id}', [WalletController::class, 'getWallet']);
    Route::apiResource('users', UserController::class)->except(['store']);
});
