<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\RiwayatGizi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Support\Facades\Http; 

class SiswaController extends Controller
{
    public function index()
    {
        // Mengambil semua siswa beserta riwayat gizi paling terbarunya
        $siswas = Siswa::with(['riwayatGizi' => function($query) {
            $query->latest();
        }])->get();

        return view('siswa.index', compact('siswas'));
    }

    public function store(Request $request)
    {
        // 1. Validasi inputan dari form
        $validated = $request->validate([
            'nisn'  => 'required',
            'nama'  => 'required',
            'kelas' => 'required',
            'umur'  => 'required|numeric',
            'tinggi'=> 'required|numeric',
            'berat' => 'required|numeric',
        ]);

        // 2. Hitung BMI dan Status Gizi secara otomatis di backend
        $tinggiMeter = $request->tinggi / 100;
        $bmi = $request->berat / ($tinggiMeter * $tinggiMeter);
        $bmi = round($bmi, 2);

        if ($bmi < 18.5) {
            $statusGizi = 'Kurus';
        } elseif ($bmi >= 18.5 && $bmi < 25) {
            $statusGizi = 'Normal';
        } elseif ($bmi >= 25 && $bmi < 30) {
            $statusGizi = 'Gemuk';
        } else {
            $statusGizi = 'Obesitas';
        }

        // 3. Cek apakah Siswa dengan NISN tersebut sudah terdaftar
        $siswa = Siswa::firstOrCreate(
            ['nisn' => $request->nisn],
            [
                'nama'  => $request->nama,
                'kelas' => $request->kelas,
            ]
        );

        // 4. Masukkan data pemeriksaan ke tabel riwayat_gizis
        RiwayatGizi::create([
            'siswa_id'    => $siswa->id,
            'umur'        => $request->umur,
            'tinggi'      => $request->tinggi,
            'berat'       => $request->berat,
            'bmi'         => $bmi,
            'status_gizi' => $statusGizi,
        ]);

        return redirect()->back()->with('success', 'Data pemeriksaan gizi berhasil disimpan!');
    }

    // Fungsi untuk Menampilkan Grafik, Histori Lengkap Siswa, dan Rekomendasi AI Gemini
    public function show($id)
    {
        // 1. Ambil data siswa dengan riwayat terlama -> terbaru untuk grafik
        $siswa = Siswa::with(['riwayatGizi' => function($query) {
            $query->orderBy('created_at', 'asc');
        }])->findOrFail($id);

        // 2. Ambil pemeriksaan TERBARU untuk dijadikan bahan prompt AI
        $kondisiTerkini = $siswa->riwayatGizi()->latest()->first();
        $rekomendasiAI = "Data pemeriksaan gizi belum tersedia untuk dianalisis oleh AI.";

        if ($kondisiTerkini) {
            try {
                $apiKey = 'AQ.Ab8RN6IEdry7utr36MS-b6vlTe6rGwyC_2GFwHkitrTSQHpUgA';
                
                // Susun prompt dinamis sesuai kondisi fisik siswa
                $prompt = "Kamu adalah ahli gizi anak profesional dari Kementerian Kesehatan RI. " .
                          "Berikan rekomendasi menu makanan harian lokal Indonesia selama 1 minggu untuk anak bernama " . $siswa->nama . " " .
                          "berumur " . $kondisiTerkini->umur . " tahun, dengan Tinggi " . $kondisiTerkini->tinggi . " cm, " .
                          "Berat " . $kondisiTerkini->berat . " kg, dan berstatus Gizi '" . $kondisiTerkini->status_gizi . "'. " .
                          "Format respons harus langsung ke poin menu per hari, ramah, edukatif, ringkas, dan gunakan format teks Markdown.";

                // PERBAIKAN: Ditambahkan .timeout(30) agar sistem sabar menunggu jaringan internet yang lambat
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
                    // Mengambil text output dari struktur JSON respons Gemini
                    $rekomendasiAI = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Gagal memproses teks dari AI.';
                } else {
                    $rekomendasiAI = "Gagal menghubungi Gemini API. Kode Status: " . $response->status();
                }

            } catch (\Exception $e) {
                $rekomendasiAI = "Gagal memuat rekomendasi AI: " . $e->getMessage();
            }
        }

        // Kirim variabel 'rekomendasiAI' ke halaman Blade
        return view('siswa.show', compact('siswa', 'rekomendasiAI'));
    }

    // Fungsi untuk memproses cetak dokumen PDF Rapor Gizi
    public function exportPdf($id)
    {
        // Ambil data siswa beserta riwayat gizinya, urutkan dari yang terbaru untuk tabel laporan
        $siswa = Siswa::with(['riwayatGizi' => function($query) {
            $query->latest();
        }])->findOrFail($id);

        // Load view khusus cetak PDF dan kirim variabel datanya
        $pdf = Pdf::loadView('siswa.pdf', compact('siswa'));
        
        // Kembalikan file PDF agar otomatis terunduh di browser pengguna
        return $pdf->download('Rapor_Gizi_' . str_replace(' ', '_', $siswa->nama) . '.pdf');
    }
}