<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    // Kita daftarkan kolom yang boleh diisi secara massal
    protected $fillable = [
        'nisn',
        'nama',
        'kelas'
    ];

    // Hubungkan relasi ke tabel RiwayatGizi (One to Many)
    public function riwayatGizi()
    {
        return $this->hasMany(RiwayatGizi::class, 'siswa_id');
    }
}