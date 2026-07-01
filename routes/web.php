<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiswaController;

Route::get('/', function () {
    return redirect('/siswa');
});

Route::resource('siswa', SiswaController::class);
Route::get('/siswa/{siswa}/export-pdf', [App\Http\Controllers\SiswaController::class, 'exportPdf'])->name('siswa.pdf');