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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            // Menghubungkan siswa ke tabel sekolah secara kaku (Foreign Key)
            // onDelete('cascade') artinya jika data sekolah dihapus, data siswa di dalamnya otomatis ikut terhapus bersih
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            
            $table->string('nisn', 12)->unique(); // NISN nasional bersifat unik
            $table->string('name');
            $table->string('class_name'); // Nama kelas (misal: 1-A, 4-B)
            $table->string('jenis_kelamin'); // Berisi string biasa 'Laki-laki' atau 'Perempuan'
            $table->date('birth_date'); // Tanggal lahir untuk perhitungan presisi usia dalam bulan oleh core engine
            $table->timestamps();
            
            // Indeks untuk mengoptimalkan kueri pencarian siswa per kelas di dasbor operasional guru
            $table->index(['school_id', 'class_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};