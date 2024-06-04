<?php

use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);

    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transaction/{transaction}', [TransactionController::class, 'show']);
    Route::post('/transaction-store', [TransactionController::class, 'store']);
    Route::post('/transaction-update/{transaction}', [TransactionController::class, 'update']);
    Route::post('/transaction-delete/{transaction}', [TransactionController::class, 'destroy']);
});
