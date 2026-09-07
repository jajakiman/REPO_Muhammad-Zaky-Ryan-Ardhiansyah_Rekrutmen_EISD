<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Reporter\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/daftar', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/daftar', [RegisteredUserController::class, 'store']);
    Route::get('/masuk', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/masuk', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::view('/pelapor', 'dashboards.reporter')->middleware('role:reporter')->name('reporter.dashboard');
    Route::middleware('role:reporter')->prefix('pelapor')->name('reporter.')->group(function () {
        Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    });
    Route::view('/petugas', 'dashboards.officer')->middleware('role:officer')->name('officer.dashboard');
    Route::view('/admin', 'dashboards.admin')->middleware('role:admin')->name('admin.dashboard');
});
