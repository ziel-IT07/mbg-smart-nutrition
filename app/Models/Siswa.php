<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    // Mengunci nama tabel secara kaku agar sinkron dengan database MySQL
    protected $table = 'siswas';

    // Mengunci kolom yang boleh diisi secara massal lewat form/request
    protected $fillable = [
        'school_id', 
        'nisn', 
        'name', 
        'class_name', 
        'jenis_kelamin', 
        'birth_date'
    ];

    /**
     * Relasi Kardinalitas: Siswa Berada di Satu Sekolah (Many to 1)
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Relasi Kardinalitas: Satu Siswa memiliki Banyak Riwayat Pemeriksaan Fisik (1 to Many)
     * Ditambahkan ->latest() agar secara default Laravel mengambil data pemeriksaan paling baru
     */
    public function riwayatAntropometri(): HasMany
    {
        return $this->hasMany(RiwayatAntropometri::class, 'siswa_id')->latest();
    }
}