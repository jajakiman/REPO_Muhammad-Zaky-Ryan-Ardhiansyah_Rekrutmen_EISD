<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/daftar', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/daftar', [RegisteredUserController::class, 'store']);
});

// Login is implemented in the next authentication slice; this named endpoint keeps registration navigation live.
Route::view('/masuk', 'auth.login')->name('login');
