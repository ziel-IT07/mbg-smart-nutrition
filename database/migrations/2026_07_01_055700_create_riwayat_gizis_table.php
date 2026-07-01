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
        Schema::create('riwayat_gizis', function (Blueprint $table) {
            $table->id();
            // Menghubungkan riwayat ini ke id di tabel siswa (Foreign Key)
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->integer('umur'); // Umur saat diperiksa
            $table->float('tinggi');
            $table->float('berat');
            $table->float('bmi');
            $table->string('status_gizi');
            $table->timestamps(); // Mencatat tanggal periksa otomatis (created_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_gizis');
    }
};