<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi massal
    protected $fillable = [
        'school_code',
        'name',
        'address',
        'district',
        'city'
    ];

    /**
     * Relasi: Satu sekolah memiliki banyak siswa (1 to Many)
     */
    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'school_id');
    }
}