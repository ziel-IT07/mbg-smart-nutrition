<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $fillable = [
        'nisn',
        'nama',
        'kelas',
        'umur',
        'tinggi',
        'berat',
        'bmi',
        'status_gizi'
    ];
}