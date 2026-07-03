<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiswaController;

// Mengarahkan ke fungsi khusus API 'storeFromApi'
Route::post('/siswa/hitung-stunting', [SiswaController::class, 'storeFromApi']);