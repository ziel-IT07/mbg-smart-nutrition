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
        Schema::create('riwayat_antropometris', function (Blueprint $table) {
            $table->id();
            // Menghubungkan log pemeriksaan ke data siswa secara kaku (Foreign Key)
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            
            $table->date('measurement_date'); // Tanggal dilakukannya pemeriksaan fisik
            $table->integer('age_in_months'); // Usia riil anak dalam bulan pada tanggal pemeriksaan
            
            // Penggunaan float untuk mendukung presisi angka desimal hasil timbangan dan stadiometer
            $table->float('weight_kg'); // Berat badan (kg)
            $table->float('height_cm'); // Tinggi badan (cm)
            $table->float('bmi_value'); // Hasil kalkulasi berat (kg) / (tinggi(m) kuadrat)
            
            // Kolom penyimpanan skor deviasi epidemiologi gizi (Z-Score)
            $table->float('zscore_tbu');  // Indikator Tinggi Badan menurut Usia (Deteksi Stunting)
            $table->float('zscore_bmu');  // Indikator Berat Badan menurut Usia (Deteksi Underweight)
            $table->float('zscore_imtu'); // Indikator Indeks Massa Tubuh menurut Usia (Deteksi Wasting/Obesitas)
            
            // Kolom kesimpulan teks untuk mempermudah pembacaan di dasbor non-medis
            $table->string('gizi_status_conclusion'); // Hasil konklusi IMT/U (misal: Gizi Baik, Obesitas)
            $table->string('stunting_status_conclusion'); // Hasil konklusi TB/U (misal: Normal, Stunted)
            $table->timestamps();
            
            // Indeks vital untuk mempercepat rendering data grafik tren pertumbuhan anak (Chart.js)
            $table->index(['siswa_id', 'measurement_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_antropometris');
    }
};