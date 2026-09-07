<?php

use App\Http\Controllers\Admin\AccessibilityFeatureController;
use App\Http\Controllers\Admin\CampusAreaController;
use App\Http\Controllers\Admin\CampusController;
use App\Http\Controllers\Admin\CampusLocationController;
use App\Http\Controllers\Admin\IssueCategoryController;
use App\Http\Controllers\Admin\LocationAccessibilityFeatureController;
use App\Http\Controllers\Admin\OfficerAccountController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HomeController;
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
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::view('/', 'dashboards.admin')->name('dashboard');
        Route::get('/kampus', [CampusController::class, 'index'])->name('campuses.index');
        Route::get('/kampus/tambah', [CampusController::class, 'create'])->name('campuses.create');
        Route::post('/kampus', [CampusController::class, 'store'])->name('campuses.store');
        Route::get('/kampus/{campus}/ubah', [CampusController::class, 'edit'])->name('campuses.edit');
        Route::put('/kampus/{campus}', [CampusController::class, 'update'])->name('campuses.update');
        Route::patch('/kampus/{campus}/nonaktifkan', [CampusController::class, 'deactivate'])->name('campuses.deactivate');
        Route::get('/kampus/{campus}/area', [CampusAreaController::class, 'index'])->name('campuses.areas.index');
        Route::get('/kampus/{campus}/area/tambah', [CampusAreaController::class, 'create'])->name('campuses.areas.create');
        Route::post('/kampus/{campus}/area', [CampusAreaController::class, 'store'])->name('campuses.areas.store');
        Route::get('/kampus/{campus}/area/{area}/ubah', [CampusAreaController::class, 'edit'])->name('campuses.areas.edit');
        Route::put('/kampus/{campus}/area/{area}', [CampusAreaController::class, 'update'])->name('campuses.areas.update');
        Route::patch('/kampus/{campus}/area/{area}/nonaktifkan', [CampusAreaController::class, 'deactivate'])->name('campuses.areas.deactivate');
        Route::get('/kampus/{campus}/area/{area}/lokasi', [CampusLocationController::class, 'index'])->name('campuses.areas.locations.index');
        Route::get('/kampus/{campus}/area/{area}/lokasi/tambah', [CampusLocationController::class, 'create'])->name('campuses.areas.locations.create');
        Route::post('/kampus/{campus}/area/{area}/lokasi', [CampusLocationController::class, 'store'])->name('campuses.areas.locations.store');
        Route::get('/kampus/{campus}/area/{area}/lokasi/{location}/ubah', [CampusLocationController::class, 'edit'])->name('campuses.areas.locations.edit');
        Route::put('/kampus/{campus}/area/{area}/lokasi/{location}', [CampusLocationController::class, 'update'])->name('campuses.areas.locations.update');
        Route::patch('/kampus/{campus}/area/{area}/lokasi/{location}/nonaktifkan', [CampusLocationController::class, 'deactivate'])->name('campuses.areas.locations.deactivate');
        Route::get('/fasilitas', [AccessibilityFeatureController::class, 'index'])->name('features.index');
        Route::get('/fasilitas/tambah', [AccessibilityFeatureController::class, 'create'])->name('features.create');
        Route::post('/fasilitas', [AccessibilityFeatureController::class, 'store'])->name('features.store');
        Route::get('/fasilitas/{feature}/ubah', [AccessibilityFeatureController::class, 'edit'])->name('features.edit');
        Route::put('/fasilitas/{feature}', [AccessibilityFeatureController::class, 'update'])->name('features.update');
        Route::patch('/fasilitas/{feature}/nonaktifkan', [AccessibilityFeatureController::class, 'deactivate'])->name('features.deactivate');
        Route::get('/kategori-masalah', [IssueCategoryController::class, 'index'])->name('issue-categories.index');
        Route::get('/kategori-masalah/tambah', [IssueCategoryController::class, 'create'])->name('issue-categories.create');
        Route::post('/kategori-masalah', [IssueCategoryController::class, 'store'])->name('issue-categories.store');
        Route::get('/kategori-masalah/{issue_category}/ubah', [IssueCategoryController::class, 'edit'])->name('issue-categories.edit');
        Route::put('/kategori-masalah/{issue_category}', [IssueCategoryController::class, 'update'])->name('issue-categories.update');
        Route::patch('/kategori-masalah/{issue_category}/nonaktifkan', [IssueCategoryController::class, 'deactivate'])->name('issue-categories.deactivate');
        Route::get('/lokasi/{location}/fasilitas', [LocationAccessibilityFeatureController::class, 'index'])->name('locations.features.index');
        Route::get('/lokasi/{location}/fasilitas/tambah', [LocationAccessibilityFeatureController::class, 'create'])->name('locations.features.create');
        Route::post('/lokasi/{location}/fasilitas', [LocationAccessibilityFeatureController::class, 'store'])->name('locations.features.store');
        Route::get('/lokasi/{location}/fasilitas/{locationFeature}/ubah', [LocationAccessibilityFeatureController::class, 'edit'])->name('locations.features.edit');
        Route::put('/lokasi/{location}/fasilitas/{locationFeature}', [LocationAccessibilityFeatureController::class, 'update'])->name('locations.features.update');
        Route::get('/petugas', [OfficerAccountController::class, 'index'])->name('officers.index');
        Route::get('/petugas/tambah', [OfficerAccountController::class, 'create'])->name('officers.create');
        Route::post('/petugas', [OfficerAccountController::class, 'store'])->name('officers.store');
        Route::get('/petugas/{officer}/ubah', [OfficerAccountController::class, 'edit'])->name('officers.edit');
        Route::put('/petugas/{officer}', [OfficerAccountController::class, 'update'])->name('officers.update');
        Route::patch('/petugas/{officer}/nonaktifkan', [OfficerAccountController::class, 'deactivate'])->name('officers.deactivate');
    });
});
