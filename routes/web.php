<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {

    // Dashboard (admin & user)
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // ---------- ADMIN SAJA: tambah, ubah, hapus ----------
    // Didaftarkan lebih dulu agar "/mahasiswa/create" tidak dianggap sebagai {mahasiswa}
    Route::middleware('role:admin')->group(function () {
        Route::get('/mahasiswa/create', [MahasiswaController::class, 'create'])->name('mahasiswa.create');
        Route::post('/mahasiswa', [MahasiswaController::class, 'store'])->name('mahasiswa.store');
        Route::get('/mahasiswa/{mahasiswa}/edit', [MahasiswaController::class, 'edit'])->whereNumber('mahasiswa')->name('mahasiswa.edit');
        Route::put('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'update'])->whereNumber('mahasiswa')->name('mahasiswa.update');
        Route::delete('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'destroy'])->whereNumber('mahasiswa')->name('mahasiswa.destroy');
    });

    // ---------- ADMIN & USER: melihat data ----------
    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
    Route::get('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'show'])->whereNumber('mahasiswa')->name('mahasiswa.show');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
