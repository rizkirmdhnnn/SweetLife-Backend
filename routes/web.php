<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/verification-success', function () {
    return view('mail.verify-success');
})->name('verify.success');


Route::get('/healthcheck', function () {
    return response()->json([
        'status' => true,
        'message' => 'API is running',
    ]);
});
