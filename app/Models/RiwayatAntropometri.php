<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatAntropometri extends Model
{
    // Mengunci nama tabel transaksional di database MySQL
    protected $table = 'riwayat_antropometris';

    // Proteksi mass assignment untuk seluruh indikator gizi klinis
    protected $fillable = [
        'siswa_id', 
        'measurement_date', 
        'age_in_months', 
        'weight_kg', 
        'height_cm', 
        'bmi_value', 
        'zscore_tbu', 
        'zscore_bmu', 
        'zscore_imtu', 
        'gizi_status_conclusion', 
        'stunting_status_conclusion'
    ];

    /**
     * Relasi Kardinalitas: Catatan Antropometri ini Merujuk pada Satu Siswa (Many to 1)
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}