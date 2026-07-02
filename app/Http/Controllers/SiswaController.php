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
        // 1. Validasi inputan dari form (Ditambahkan jenis_kelamin)
        $validated = $request->validate([
            'nisn'          => 'required',
            'nama'          => 'required',
            'kelas'         => 'required',
            'umur'          => 'required|numeric',
            'tinggi'        => 'required|numeric',
            'berat'         => 'required|numeric',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan', 
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

        // 3. Cek apakah Siswa dengan NISN tersebut sudah terdaftar (Ditambahkan jenis_kelamin)
        $siswa = Siswa::firstOrCreate(
            ['nisn' => $request->nisn],
            [
                'nama'          => $request->nama,
                'kelas'         => $request->kelas,
                'jenis_kelamin' => $request->jenis_kelamin, 
            ]
        );

        // 4. Masukkan data pemeriksaan ke tabel riwayat_gizis
        // Catatan: status_tbu diisi 'Pending' dahulu, nanti akan diperbarui/dianalisis oleh AI Gemini di halaman detail
        RiwayatGizi::create([
            'siswa_id'    => $siswa->id,
            'umur'        => $request->umur,
            'tinggi'      => $request->tinggi,
            'berat'       => $request->berat,
            'bmi'         => $bmi,
            'status_gizi' => $statusGizi,
            'status_tbu'  => 'Pending', 
        ]);

        return redirect()->back()->with('success', 'Data pemeriksaan gizi berhasil disimpan!');
    }

    // Fungsi untuk Menampilkan Grafik, Histori Lengkap Siswa, dan Rekomendasi AI Gemini + Deteksi Stunting
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
                // Mengambil API Key dari .env agar lebih aman sesuai standar kompetisi
                $apiKey = env('GEMINI_API_KEY', 'AQ.Ab8RN6IEdry7utr36MS-b6vlTe6rGwyC_2GFwHkitrTSQHpUgA');
                
                // PERBAIKAN PROMPT: Meminta Gemini melakukan deteksi Stunting (TB/U) berdasarkan standar Kemenkes/WHO
                $prompt = "Kamu adalah ahli gizi anak profesional dari Kementerian Kesehatan RI. " .
                          "Lakukan analisis gizi ganda (BMI dan Indikator TB/U untuk deteksi stunting) untuk anak bernama " . $siswa->nama . " berjenis kelamin " . $siswa->jenis_kelamin . ". " .
                          "Data fisik saat ini: Umur " . $kondisiTerkini->umur . " tahun, Tinggi " . $kondisiTerkini->tinggi . " cm, Berat " . $kondisiTerkini->berat . " kg, dan BMI " . $kondisiTerkini->bmi . " (" . $kondisiTerkini->status_gizi . "). " .
                          "Tugasmu:\n" .
                          "1. Berdasarkan standar Antropometri Kemenkes RI/WHO, tentukan apakah tinggi badan anak ini termasuk Sangat Pendek (Severely Stunted), Pendek (Stunted), Normal, atau Tinggi.\n" .
                          "2. Berikan kesimpulan apakah anak ini terindikasi stunting atau tidak.\n" .
                          "3. Berikan rekomendasi menu makanan harian lokal Indonesia selama 1 minggu yang kaya akan protein hewani (seperti telur, ikan, susu) untuk mengoptimalkan pertumbuhannya.\n" .
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
                    
                    // FITUR OTOMATIS: Jika respons mengandung kata stunting/pendek, kita update status_tbu di database secara pintar
                    if (str_contains(strtolower($rekomendasiAI), 'stunted') || str_contains(strtolower($rekomendasiAI), 'pendek')) {
                        $kondisiTerkini->update(['status_tbu' => 'Stunted']);
                    } else {
                        $kondisiTerkini->update(['status_tbu' => 'Normal']);
                    }
                } else {
                    $rekomendasiAI = "Gagal menghubungi Gemini API. Kode Status: " . $response->status();
                }

            } catch (\Exception $e) {
                $rekomendasiAI = "Gagal memuat rekomendasi AI: " . $e->getMessage();
            }
        }

        return view('siswa.show', compact('siswa', 'rekomendasiAI'));
    }

    // Fungsi untuk memproses cetak dokumen PDF Rapor Gizi
    public function exportPdf($id)
    {
        $siswa = Siswa::with(['riwayatGizi' => function($query) {
            $query->latest();
        }])->findOrFail($id);

        $pdf = Pdf::loadView('siswa.pdf', compact('siswa'));
        
        return $pdf->download('Rapor_Gizi_' . str_replace(' ', '_', $siswa->nama) . '.pdf');
    }
}