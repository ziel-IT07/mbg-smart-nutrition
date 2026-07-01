<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatGizi extends Model
{
    // Mengizinkan kolom-kolom ini diisi data secara massal
    protected $fillable = [
        'siswa_id',
        'umur',
        'tinggi',
        'berat',
        'bmi',
        'status_gizi'
    ];

    // Hubungkan balik relasi ke model Siswa (Belongs To)
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}