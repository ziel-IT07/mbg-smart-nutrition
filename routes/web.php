<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiswaController;

Route::get('/', function () {
    return redirect('/siswa');
});

Route::resource('siswa', SiswaController::class);