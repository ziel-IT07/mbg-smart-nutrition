<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;

Route::get('/', function () {
    return redirect('/login');
});

// ==========================================
// Route untuk tamu (belum login)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ==========================================
// Route untuk user yang sudah login (admin & guru)
// ==========================================
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard, Grafik, Export PDF -> admin & guru boleh akses
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/siswa/{siswa}/export-pdf', [SiswaController::class, 'exportPdf'])
        ->name('siswa.pdf');

    // Melihat daftar & detail siswa -> admin & guru boleh (tidak dibatasi role)
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/{siswa}', [SiswaController::class, 'show'])->name('siswa.show');

    // ==========================================
    // Kelola Data Siswa & Input Pengukuran -> HANYA guru
    // (SiswaController hanya punya method index, store, show, exportPdf,
    //  jadi TIDAK memakai Route::resource, cukup route manual berikut)
    // ==========================================
    Route::middleware('role:guru')->group(function () {
        Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    });

    // ==========================================
    // Kelola Akun -> HANYA admin
    // Contoh route, sesuaikan dengan controller kamu nanti
    // ==========================================
    // Route::middleware('role:admin')->group(function () {
    //     Route::resource('akun', AkunController::class);
    // });

});