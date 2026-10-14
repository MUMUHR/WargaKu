<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Autentikasi (Laravel Breeze — Disesuaikan untuk WargaKu)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login Warga & Admin RT
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.post');

    // Registrasi Akun Warga Baru (Kepala Keluarga)
    Route::get('/daftar', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/daftar', [RegisteredUserController::class, 'store'])
        ->name('register.post');
});

// Logout dari Sesi Akun
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
