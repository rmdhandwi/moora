<?php

use App\Http\Controllers\Auth\AuthLogin;
use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {

    // Rute halaman login
    Route::get('/', function () {
        return Inertia::render('Auth/LoginPage');
    })->name('login');

    // Rute halaman registrasi
    Route::get('/register', function () {
        return Inertia::render('Auth/RegisterPage');
    })->name('register');

    // Penanganan form login dan registrasi
    Route::post('authlogin', [AuthLogin::class, 'login'])->name('LoginSubmit');
    Route::post('authregister', [AuthLogin::class, 'register'])->name('registerSubmit');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LogoutController::class, 'logout'])->name('logout');
});
