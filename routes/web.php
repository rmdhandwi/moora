<?php

use App\Http\Controllers\Angkatan;
use Illuminate\Foundation\Application;
use App\Http\Controllers\Auth\AuthLogin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Dosen;
use App\Http\Controllers\Kriteria;
use App\Http\Controllers\Mahasiswa;
use App\Http\Controllers\Perhitungan;
use App\Http\Controllers\Users;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {

    // Rute halaman login
    Route::get('/', function () {
        return Inertia::render('Auth/LoginPage');
    })->name('login');
});



Route::middleware('auth')->group(function () {

    // Rute halaman dashboard
    Route::get('Dashboard', [Dashboard::class, 'DashboardPage'])->name('dashboard');

    Route::get('Users', [Users::class, 'usersPage'])->name('usersPage');
    Route::post('Users/store', [Users::class, 'store'])->name('create.user');
    Route::put('Users/{id}/update', [Users::class, 'update'])->name('update.user');
    Route::delete('Users/{id}/delete', [Users::class, 'destroy'])->name('destroy.user');

    Route::get('Dosen', [Dosen::class, 'dosenPage'])->name('dosenPage');
    Route::post('Dosen/store', [Dosen::class, 'store'])->name('create.dosen');
    Route::put('Dosen/{id}/update', [Dosen::class, 'update'])->name('update.dosen');
    Route::delete('Dosen/{id}/delete', [Dosen::class, 'destroy'])->name('destroy.dosen');

    Route::get('Angkatan', [Angkatan::class, 'angkatanPage'])->name('angkatanPage');
    Route::post('Angkatan/store', [Angkatan::class, 'store'])->name('create.angkatan');
    Route::put('Angkatan/{id}/update', [Angkatan::class, 'update'])->name('update.angkatan');
    Route::delete('Angkatan/{id}/delete', [Angkatan::class, 'destroy'])->name('destroy.angkatan');

    Route::get('Kriteria', [Kriteria::class, 'kriteriaPage'])->name('kriteriaPage');
    Route::post('Kriteria/store', [Kriteria::class, 'store'])->name('create.kriteria');
    Route::put('Kriteria/{id}/update', [Kriteria::class, 'update'])->name('update.kriteria');
    Route::delete('Kriteria/{id}/delete', [Kriteria::class, 'destroy'])->name('destroy.kriteria');

    Route::get('Mahasiswa', [Mahasiswa::class, 'mahasiswaPage'])->name('mahasiswaPage');
    Route::post('Mahasiswa/store', [Mahasiswa::class, 'store'])->name('create.mahasiswa');
    Route::put('Mahasiswa/{id}/update', [Mahasiswa::class, 'update'])->name('update.mahasiswa');
    Route::delete('Mahasiswa/{id}/delete', [Mahasiswa::class, 'destroy'])->name('destroy.mahasiswa');

    Route::post('Import', [Mahasiswa::class, 'uploadCSV'])->name('uploadCSV');

    Route::get('Perhitungan', [Perhitungan::class, 'perhitunganPage'])->name('perhitunganPage');
    Route::put('Perhitungan/{id}/moora/admin', [Perhitungan::class, 'store'])->name('put.perhitungan');
    Route::put('Perhitungan/{id}/moora/dosen', [Perhitungan::class, 'create'])->name('create.perhitungan');
});

require __DIR__ . '/auth.php';
