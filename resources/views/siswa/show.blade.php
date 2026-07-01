<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Perkembangan Gizi - {{ $siswa->nama }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📊 Riwayat Gizi Bulanan</h2>
        <div>
            <a href="{{ route('siswa.pdf', ['siswa' => $siswa->id]) }}" class="btn btn-danger me-2">
                📄 Cetak Rapor Gizi (PDF)
            </a>
            <a href="{{ route('siswa.index') }}" class="btn btn-secondary">⬅️ Kembali</a>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-dark text-white fw-bold">Profil Siswa</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Nama:</strong> {{ $siswa->nama }}</div>
                <div class="col-md-4"><strong>NISN:</strong> {{ $siswa->nisn }}</div>
                <div class="col-md-4"><strong>Kelas:</strong> {{ $siswa->kelas }}</div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">📈 Grafik Tren Perkembangan BMI</div>
        <div class="card-body">
            <div style="width: 100%; height: 350px;">
                <canvas id="bmiChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm border-primary">
        <div class="card-header bg-gradient bg-primary text-white fw-bold">
            <span>✨ AI Smart Nutritionist Recommendation (Gemini API Direct Fetch)</span>
        </div>
        <div class="card-body bg-white">
            <div id="ai-response" class="p-2" style="line-height: 1.7; font-size: 15px; color: #2b2b2b;">
                <div class="d-flex align-items-center text-muted">
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Menghubungi Google Gemini AI untuk merangkai menu gizi...
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white fw-bold">📋 Tabel Catatan Pemeriksaan</div>
        <div class="card-body">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal Periksa</th>
                        <th>Umur</th>
                        <th>Tinggi Badan</th>
                        <th>Berat Badan</th>
                        <th>BMI</th>
                        <th>Status Gizi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswa->riwayatGizi as $riwayat)
                    <tr>
                        <td>{{ $riwayat->created_at->format('d M Y - H:i') }}</td>
                        <td>{{ $riwayat->umur }} Tahun</td>
                        <td>{{ $riwayat->tinggi }} cm</td>
                        <td>{{ $riwayat->berat }} kg</td>
                        <td class="fw-bold">{{ $riwayat->bmi }}</td>
                        <td>
                            @if($riwayat->status_gizi == 'Kurus')
                                <span class="badge bg-warning text-dark">Kurus</span>
                            @elseif($riwayat->status_gizi == 'Normal')
                                <span class="badge bg-success">Normal</span>
                            @elseif($riwayat->status_gizi == 'Gemuk')
                                <span class="badge bg-info text-dark">Gemuk</span>
                            @else
                                <span class="badge bg-danger">Obesitas</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Menyiapkan data dari database Laravel ke JavaScript
    const labelsTanggal = [
        @foreach($siswa->riwayatGizi as $riwayat)
            "{{ $riwayat->created_at->format('d M Y') }}",
        @endforeach
    ];

    const dataBMI = [
        @foreach($siswa->riwayatGizi as $riwayat)
            {{ $riwayat->bmi }},
        @endforeach
    ];

    // Konfigurasi Chart.js
    const ctx = document.getElementById('bmiChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labelsTanggal,
            datasets: [{
                label: 'Nilai BMI (Body Mass Index)',
                data: dataBMI,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 3,
                pointBackgroundColor: '#0d6efd',
                pointRadius: 6,
                pointHoverRadius: 8,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: 'Skala BMI'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Tanggal Pemeriksaan'
                    }
                }
            }
        }
    });

    // OPSI BARU: Mengirim request ke Gemini API langsung melalui Sisi Browser Laptop (JavaScript)
    document.addEventListener("DOMContentLoaded", async function() {
        const aiResponseDiv = document.getElementById('ai-response');
        
        // 1. SILAKAN MASUKKAN KUNCI API TERBARU KAMU DI SINI
        const apiKey = "AQ.Ab8RN6IEdry7utr36MS-b6vlTe6rGwyC_2GFwHkitrTSQHpUgA"; 
        
        // 2. Mengumpulkan parameter kondisi gizi siswa dari data Laravel secara aman
        const namaSiswa = "{{ $siswa->nama }}";
        const umurSiswa = "{{ $siswa->riwayatGizi->last()->umur ?? 0 }}";
        const tinggiSiswa = "{{ $siswa->riwayatGizi->last()->tinggi ?? 0 }}";
        const beratSiswa = "{{ $siswa->riwayatGizi->last()->berat ?? 0 }}";
        const statusGizi = "{{ $siswa->riwayatGizi->last()->status_gizi ?? 'Normal' }}";

        if(umurSiswa == 0) {
            aiResponseDiv.innerHTML = "<span class='text-muted'>Data pemeriksaan gizi belum tersedia.</span>";
            return;
        }

        // 3. Susun instruksi prompt ahli gizi
        const promptText = `Kamu adalah ahli gizi anak profesional dari Kementerian Kesehatan RI. Berikan rekomendasi menu makanan harian lokal Indonesia selama 1 minggu untuk anak bernama ${namaSiswa} berumur ${umurSiswa} tahun, dengan Tinggi ${tinggiSiswa} cm, Berat ${beratSiswa} kg, dan berstatus Gizi '${statusGizi}'. Format respons harus langsung ke poin menu per hari, ramah, edukatif, ringkas, dan gunakan format teks Markdown.`;

        try {
            // 4. Lakukan pemanggilan Fetch langsung ke server Google
            const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=${apiKey}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    contents: [{
                        parts: [{ text: promptText }]
                    }]
                })
            });

            const result = await response.json();

            if (response.ok) {
                // 5. Sukses! Parsing Markdown dari Gemini ke HTML bersih menggunakan Marked.js
                const markdownText = result.candidates[0].content.parts[0].text;
                aiResponseDiv.innerHTML = marked.parse(markdownText);
            } else {
                // Jika masih ada kendala pada akun, browser akan menampilkan pesan detail aslinya dari Google
                aiResponseDiv.innerHTML = `<span class='text-danger'>Gagal memuat rekomendasi (${response.status}). <br>Pesan Google: ${result.error.message}</span>`;
            }

        } catch (error) {
            aiResponseDiv.innerHTML = "<span class='text-danger'>Terjadi gangguan jaringan atau browser gagal terhubung ke Gemini API.</span>";
        }
    });
</script>

</body>
</html>