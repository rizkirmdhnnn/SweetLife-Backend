<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\UserVerifiedMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {

    // Verification Email
    Route::get('/verify-email/{id}', AuthController::class . '@VerifyEmail');

    // Auth
    Route::prefix('/auth')->group(function () {
        Route::post('/register', AuthController::class . '@Register');
        Route::post('/login', AuthController::class . '@Login');
        Route::post('/logout', AuthController::class . '@Logout')->middleware(JwtMiddleware::class);
    });
});
