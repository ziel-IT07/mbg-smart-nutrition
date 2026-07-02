<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('school_code')->unique(); // NPSN Sekolah (Wajib Unik)
            $table->string('name'); // Nama Satuan Pendidikan (misal: SDN 01 Menteng)
            $table->string('district'); // Kecamatan (untuk pemetaan risiko mikro)
            $table->string('city'); // Kabupaten atau Kota
            $table->string('latitude')->nullable(); // Koordinat Geo-Spasial
            $table->string('longitude')->nullable(); // Koordinat Geo-Spasial
            $table->timestamps();
            
            // Indeks komposit vital untuk mempercepat kueri analitik regional pemerintah
            $table->index(['district', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};