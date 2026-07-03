<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\School;
use App\Services\ZScoreEngine;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Support\Facades\Http; 

class SiswaController extends Controller
{
    public function index()
    {
        // Mengambil semua siswa beserta sekolah dan riwayat antropometri terbarunya
        $siswas = Siswa::with(['school', 'riwayatAntropometri' => function($query) {
            $query->latest();
        }])->get();

        return view('siswa.index', compact('siswas'));
    }

    public function store(Request $request)
    {
        // 1. Validasi inputan dari form sesuai kolom baru database MySQL
        $validated = $request->validate([
            'school_id'     => 'required|exists:schools,id',
            'nisn'          => 'required|string|unique:siswas,nisn',
            'nama'          => 'required|string|max:255',
            'kelas'         => 'required|string',
            'jenis_kelamin' => 'required|in:L,P', // Menggunakan inisial 'L' atau 'P' sesuai database
            'birth_date'    => 'required|date',
            'umur_bulan'    => 'required|integer|min:0', // Standar WHO menggunakan hitungan bulan
            'tinggi'        => 'required|numeric|min:0',
            'berat'         => 'required|numeric|min:0',
        ]);

        // 2. Hitung BMI secara otomatis
        $tinggiMeter = $validated['tinggi'] / 100;
        $bmi = round($validated['berat'] / ($tinggiMeter * $tinggiMeter), 2);

        // Contoh status gizi sederhana berdasarkan BMI
        $statusGizi = 'Gizi Baik (Normal)';
        if ($bmi < 18.5) { $statusGizi = 'Gizi Kurang'; }
        elseif ($bmi >= 25) { $statusGizi = 'Gizi Lebih / Obesitas'; }

        // 3. Hitung Z-Score Stunting secara akurat via Core Engine (Fase 1)
        // Nilai median/SD ini contoh, idealnya ditarik berdasarkan data WHO/Kemenkes
        $zScoreTBU = ZScoreEngine::calculate($validated['tinggi'], 106.1, 102.1, 110.2);
        $statusStunting = ZScoreEngine::interpretStunting($zScoreTBU);

        // 4. Buat atau cari siswa
        $siswa = Siswa::create([
            'school_id'     => $validated['school_id'],
            'nisn'          => $validated['nisn'],
            'name'          => $validated['nama'], // disesuaikan ke kolom 'name'
            'class_name'    => $validated['kelas'], // disesuaikan ke kolom 'class_name'
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'birth_date'    => $validated['birth_date'],
        ]);

        // 5. Masukkan data pemeriksaan ke tabel riwayat_antropometris yang baru
        $siswa->riwayatAntropometri()->create([
            'measurement_date'           => now()->toDateString(),
            'age_in_months'              => $validated['umur_bulan'],
            'weight_kg'                  => $validated['berat'],
            'height_cm'                  => $validated['tinggi'],
            'bmi_value'                  => $bmi,
            'zscore_tbu'                 => $zScoreTBU,
            'zscore_bmu'                 => 0.0,
            'zscore_imtu'                => 0.0,
            'gizi_status_conclusion'     => $statusGizi,
            'stunting_status_conclusion' => $statusStunting,
        ]);

        return redirect()->back()->with('success', 'Data pemeriksaan gizi & stunting berhasil disimpan!');
    }

    public function show($id)
    {
        // 1. Ambil data siswa dengan riwayat antropometri terlama -> terbaru untuk grafik
        $siswa = Siswa::with(['school', 'riwayatAntropometri' => function($query) {
            $query->orderBy('created_at', 'asc');
        }])->findOrFail($id);

        // 2. Ambil pemeriksaan TERBARU untuk bahan analisis AI
        $kondisiTerkini = $siswa->riwayatAntropometri()->latest()->first();
        $rekomendasiAI = "Data pemeriksaan gizi belum tersedia untuk dianalisis oleh AI.";

        if ($kondisiTerkini) {
            try {
                $apiKey = env('GEMINI_API_KEY', 'AQ.Ab8RN6IEdry7utr36MS-b6vlTe6rGwyC_2GFwHkitrTSQHpUgA');
                
                $prompt = "Kamu adalah ahli gizi anak profesional dari Kementerian Kesehatan RI. " .
                          "Berikan rekomendasi gizi untuk anak bernama " . $siswa->name . " (" . ($siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan') . "). " .
                          "Data fisik terkini: Umur " . $kondisiTerkini->age_in_months . " bulan, Tinggi " . $kondisiTerkini->height_cm . " cm, Berat " . $kondisiTerkini->weight_kg . " kg, BMI " . $kondisiTerkini->bmi_value . " (" . $kondisiTerkini->gizi_status_conclusion . "). " .
                          "Berdasarkan perhitungan Z-Score matematis, status pertumbuhan tinggi badannya adalah: " . $kondisiTerkini->stunting_status_conclusion . ".\n\n" .
                          "Tugasmu:\n" .
                          "1. Jelaskan secara singkat arti dari status " . $kondisiTerkini->stunting_status_conclusion . " tersebut kepada orang tua.\n" .
                          "2. Berikan rekomendasi menu makanan harian lokal Indonesia selama 1 minggu yang kaya protein hewani (telur, ikan, susu) spesifik untuk mengejar ketertinggalan atau menjaga pertumbuhannya.\n" .
                          "Format respons harus langsung ke poin, ramah, edukatif, ringkas, dan gunakan format teks Markdown.";

                $response = Http::timeout(30)->withHeaders([
                    'Content-Type' => 'application/json'
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $result = $response->json();
                    $rekomendasiAI = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Gagal memproses teks dari AI.';
                } else {
                    $rekomendasiAI = "Gagal menghubungi Gemini API. Kode Status: " . $response->status();
                }

            } catch (\Exception $e) {
                $rekomendasiAI = "Gagal memuat rekomendasi AI: " . $e->getMessage();
            }
        }

        return view('siswa.show', compact('siswa', 'rekomendasiAI'));
    }

    public function exportPdf($id)
    {
        $siswa = Siswa::with(['school', 'riwayatAntropometri' => function($query) {
            $query->latest();
        }])->findOrFail($id);

        $pdf = Pdf::loadView('siswa.pdf', compact('siswa'));
        
        return $pdf->download('Rapor_Gizi_' . str_replace(' ', '_', $siswa->name) . '.pdf');
    }
    // Tambahkan ini di bagian paling bawah file SiswaController.php sebelum tanda } penutup class
    public function storeFromApi(Request $request)
    {
        // 1. Validasi inputan JSON dari Postman (menyesuaikan nama parameter inputan)
        $validated = $request->validate([
            'school_id'     => 'required|exists:schools,id',
            'nisn'          => 'required|string|unique:siswas,nisn',
            'name'          => 'required|string|max:255',
            'class_name'    => 'required|string',
            'jenis_kelamin' => 'required|in:L,P',
            'birth_date'    => 'required|date',
            'age_in_months' => 'required|integer|min:0',
            'tinggi_cm'     => 'required|numeric|min:0',
            'weight_kg'     => 'required|numeric|min:0',
        ]);

        // 2. Hitung BMI otomatis
        $tinggiMeter = $validated['tinggi_cm'] / 100;
        $bmi = round($validated['weight_kg'] / ($tinggiMeter * $tinggiMeter), 2);

        $statusGizi = 'Gizi Baik (Normal)';
        if ($bmi < 18.5) { $statusGizi = 'Gizi Kurang'; }
        elseif ($bmi >= 25) { $statusGizi = 'Gizi Lebih / Obesitas'; }

        // 3. Hitung Z-Score Stunting via Core Engine
        $zScoreTBU = ZScoreEngine::calculate($validated['tinggi_cm'], 106.1, 102.1, 110.2);
        $statusStunting = ZScoreEngine::interpretStunting($zScoreTBU);

        // 4. Simpan ke database
        $siswa = Siswa::create([
            'school_id'     => $validated['school_id'],
            'nisn'          => $validated['nisn'],
            'name'          => $validated['name'],
            'class_name'    => $validated['class_name'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'birth_date'    => $validated['birth_date'],
        ]);

        $riwayat = $siswa->riwayatAntropometri()->create([
            'measurement_date'           => now()->toDateString(),
            'age_in_months'              => $validated['age_in_months'],
            'weight_kg'                  => $validated['weight_kg'],
            'height_cm'                  => $validated['tinggi_cm'],
            'bmi_value'                  => $bmi,
            'zscore_tbu'                 => $zScoreTBU,
            'zscore_bmu'                 => 0.0,
            'zscore_imtu'                => 0.0,
            'gizi_status_conclusion'     => $statusGizi,
            'stunting_status_conclusion' => $statusStunting,
        ]);

        // 5. Berikan response berformat JSON sukses (201 Created) ke Postman
        return response()->json([
            'success' => true,
            'message' => 'Data pemeriksaan gizi & stunting melalui API berhasil dihitung!',
            'data'    => [
                'siswa'   => $siswa,
                'riwayat' => $riwayat
            ]
        ], 201);
    }
}