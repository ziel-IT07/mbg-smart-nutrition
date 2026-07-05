<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Perkembangan Gizi - {{ $siswa->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        :root{
            --ink:#26312E; --forest:#1F6F5C; --forest-dark:#154C40;
            --turmeric:#E0A72E; --cream:#FBF7EF; --coral:#D65A46; --sage:#6FA97D;
        }
        body{ background:var(--cream); color:var(--ink); font-family:'Plus Jakarta Sans',sans-serif; }
        .font-display{ font-family:'Fraunces',serif; }
        .font-mono{ font-family:'IBM Plex Mono',monospace; }
        .card-soft{
            background:#fff; border-radius:20px; border:1px solid #ECE4D2;
            box-shadow: 0 1px 2px rgba(38,49,46,0.04), 0 10px 30px -12px rgba(38,49,46,0.08);
        }
        .sprout::before{ content:""; display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:8px; }
        .sprout-normal::before{ background:var(--sage); box-shadow:0 0 0 3px rgba(111,169,125,0.25); }
        .sprout-warn::before{ background:var(--turmeric); box-shadow:0 0 0 3px rgba(224,167,46,0.25); }
        .sprout-danger::before{ background:var(--coral); box-shadow:0 0 0 3px rgba(214,90,70,0.25); }

        .btn-ghost{
            border:1.5px solid #E4DCC8; border-radius:12px; font-weight:600; font-size:14px;
            padding:10px 18px; transition:all .15s ease; color:var(--ink);
        }
        .btn-ghost:hover{ background:#fff; border-color:#D8CFB8; }
        .btn-solid{
            background:var(--coral); color:#fff; border-radius:12px; font-weight:700; font-size:14px;
            padding:10px 18px; transition:all .15s ease;
        }
        .btn-solid:hover{ background:#B94836; transform:translateY(-1px); }

        table.gz thead th{
            font-size:11.5px; text-transform:uppercase; letter-spacing:.05em; color:#5A6660;
            font-weight:700; padding:12px 16px; text-align:left; border-bottom:1.5px solid #ECE4D2;
        }
        table.gz tbody td{ padding:14px 16px; border-bottom:1px solid #F2EDE1; font-size:14px; }
        table.gz tbody tr:hover{ background:#FBF9F3; }

        .ai-glow{
            background: linear-gradient(135deg, var(--forest) 0%, var(--forest-dark) 100%);
        }
        .spinner{
            width:16px; height:16px; border-radius:50%;
            border:2px solid rgba(255,255,255,0.3); border-top-color:#fff;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin{ to{ transform:rotate(360deg); } }
    </style>
</head>
<body class="min-h-screen">

    <div class="max-w-5xl mx-auto px-6 py-10">

        <!-- top bar -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-display text-lg text-white" style="background:var(--forest)">📈</div>
                <div>
                    <h1 class="font-display text-2xl font-semibold">Riwayat Gizi Bulanan</h1>
                    <p class="text-sm text-[#6B7570]">Pantauan pertumbuhan individual siswa</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('siswa.pdf', ['siswa' => $siswa->id]) }}" class="btn-solid">Cetak Rapor Gizi (PDF)</a>
                <a href="{{ route('siswa.index') }}" class="btn-ghost">← Kembali</a>
            </div>
        </div>

        <!-- profile -->
        <div class="card-soft p-6 mb-6">
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wide text-[#8A8272] mb-1">Nama</div>
                    <div class="font-display text-lg font-semibold">{{ $siswa->name }}</div>
                </div>
                <div>
                    <div class="text-xs font-bold uppercase tracking-wide text-[#8A8272] mb-1">NISN</div>
                    <div class="font-mono text-lg">{{ $siswa->nisn }}</div>
                </div>
                <div>
                    <div class="text-xs font-bold uppercase tracking-wide text-[#8A8272] mb-1">Kelas</div>
                    <div class="font-display text-lg font-semibold">{{ $siswa->class_name }}</div>
                </div>
            </div>
        </div>

        <!-- chart -->
        <div class="card-soft p-6 mb-6">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-1.5 h-6 rounded-full" style="background:var(--turmeric)"></div>
                <h2 class="font-display text-lg font-semibold">Grafik Tren Perkembangan BMI</h2>
            </div>
            <div style="width: 100%; height: 320px;">
                <canvas id="bmiChart"></canvas>
            </div>
        </div>

        <!-- AI recommendation -->
        <div class="card-soft mb-6 overflow-hidden">
            <div class="ai-glow px-6 py-4 flex items-center gap-2">
                <span class="text-white font-display font-semibold">✨ AI Smart Nutritionist Recommendation</span>
            </div>
            <div class="p-6">
                <div id="ai-response" class="prose-sm leading-relaxed text-[15px]">
                    <div class="flex items-center gap-2 text-[#6B7570]">
                        <span class="spinner" style="border-color:rgba(31,111,92,0.25); border-top-color:var(--forest);"></span>
                        Menghubungi Google Gemini AI untuk merangkai menu gizi...
                    </div>
                </div>
            </div>
        </div>

        <!-- history table -->
        <div class="card-soft p-6">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-1.5 h-6 rounded-full" style="background:var(--forest)"></div>
                <h2 class="font-display text-lg font-semibold">Tabel Catatan Pemeriksaan</h2>
            </div>
            <div class="overflow-x-auto -mx-2">
                <table class="gz w-full min-w-[720px]">
                    <thead>
                        <tr>
                            <th>Tanggal Periksa</th>
                            <th>Umur</th>
                            <th>Tinggi Badan</th>
                            <th>Berat Badan</th>
                            <th>BMI</th>
                            <th>Status Gizi</th>
                            <th>Status Stunting</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswa->riwayatAntropometri as $riwayat)
                        <tr>
                            <td>{{ $riwayat->created_at->format('d M Y - H:i') }}</td>
                            <td>{{ $riwayat->age_in_months }} Bulan</td>
                            <td>{{ $riwayat->height_cm }} cm</td>
                            <td>{{ $riwayat->weight_kg }} kg</td>
                            <td class="font-mono font-semibold">{{ $riwayat->bmi_value }}</td>
                            <td>
                                @if(str_contains($riwayat->gizi_status_conclusion, 'Kurang'))
                                    <span class="sprout sprout-warn text-sm font-medium">{{ $riwayat->gizi_status_conclusion }}</span>
                                @elseif(str_contains($riwayat->gizi_status_conclusion, 'Normal'))
                                    <span class="sprout sprout-normal text-sm font-medium">{{ $riwayat->gizi_status_conclusion }}</span>
                                @else
                                    <span class="sprout sprout-danger text-sm font-medium">{{ $riwayat->gizi_status_conclusion }}</span>
                                @endif
                            </td>
                            <td>
                                @if(in_array($riwayat->stunting_status_conclusion, ['Stunted','Severely Stunted','Pendek']))
                                    <span class="sprout sprout-danger text-sm font-medium">{{ $riwayat->stunting_status_conclusion }}</span>
                                @else
                                    <span class="sprout sprout-normal text-sm font-medium">{{ $riwayat->stunting_status_conclusion }}</span>
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
    const labelsTanggal = [
        @foreach($siswa->riwayatAntropometri as $riwayat)
            "{{ $riwayat->created_at->format('d M Y') }}",
        @endforeach
    ];

    const dataBMI = [
        @foreach($siswa->riwayatAntropometri as $riwayat)
            {{ $riwayat->bmi_value }},
        @endforeach
    ];

    const ctx = document.getElementById('bmiChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, 'rgba(31,111,92,0.25)');
    gradient.addColorStop(1, 'rgba(31,111,92,0.02)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labelsTanggal,
            datasets: [{
                label: 'Nilai BMI (Body Mass Index)',
                data: dataBMI,
                borderColor: '#1F6F5C',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#E0A72E',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { font: { family: "'Plus Jakarta Sans'" } } } },
            scales: {
                y: { beginAtZero: false, title: { display: true, text: 'Skala BMI', font: { family: "'Plus Jakarta Sans'" } } },
                x: { title: { display: true, text: 'Tanggal Pemeriksaan', font: { family: "'Plus Jakarta Sans'" } } }
            }
        }
    });

    // NOTE (security): the API key call below is kept only to preserve existing
    // behavior during this visual redesign. See chat notes — move this to a
    // backend-proxied route so the key is never shipped to the browser.
    document.addEventListener("DOMContentLoaded", async function() {
        const aiResponseDiv = document.getElementById('ai-response');

        const apiKey = "AQ.Ab8RN6IEdry7utr36MS-b6vlTe6rGwyC_2GFwHkitrTSQHpUgA";

        const namaSiswa = "{{ $siswa->name }}";
        const umurSiswa = "{{ $siswa->riwayatAntropometri->last()->age_in_months ?? 0 }}";
        const tinggiSiswa = "{{ $siswa->riwayatAntropometri->last()->height_cm ?? 0 }}";
        const beratSiswa = "{{ $siswa->riwayatAntropometri->last()->weight_kg ?? 0 }}";
        const statusGizi = "{{ $siswa->riwayatAntropometri->last()->gizi_status_conclusion ?? 'Normal' }}";
        const statusStunting = "{{ $siswa->riwayatAntropometri->last()->stunting_status_conclusion ?? 'Normal' }}";

        if(umurSiswa == 0) {
            aiResponseDiv.innerHTML = "<span class='text-[#8A8272]'>Data pemeriksaan gizi belum tersedia.</span>";
            return;
        }

        const promptText = `Kamu adalah ahli gizi anak profesional dari Kementerian Kesehatan RI. Berikan rekomendasi menu makanan harian lokal Indonesia selama 1 minggu untuk anak bernama ${namaSiswa} berumur ${umurSiswa} bulan, dengan Tinggi ${tinggiSiswa} cm, Berat ${beratSiswa} kg, berstatus Gizi '${statusGizi}', dan berstatus Stunting '${statusStunting}'. Format respons harus langsung ke poin menu per hari, ramah, edukatif, ringkas, dan gunakan format teks Markdown.`;

        try {
            const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=${apiKey}`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ contents: [{ parts: [{ text: promptText }] }] })
            });

            const result = await response.json();

            if (response.ok) {
                const markdownText = result.candidates[0].content.parts[0].text;
                aiResponseDiv.innerHTML = marked.parse(markdownText);
            } else {
                aiResponseDiv.innerHTML = `<span style="color:var(--coral)">Gagal membuat rekomendasi (${response.status}). <br>Pesan Google: ${result.error.message}</span>`;
            }
        } catch (error) {
            aiResponseDiv.innerHTML = "<span style='color:var(--coral)'>Terjadi gangguan jaringan atau browser gagal terhubung ke Gemini API.</span>";
        }
    });
</script>

</body>
</html>