<!DOCTYPE html>
<html>
<head>
    <title>Rapor Gizi Siswa</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #26312E; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #1F6F5C; padding-bottom: 12px; }
        .header h2 { margin: 0; font-size: 19px; text-transform: uppercase; color: #154C40; letter-spacing: 0.5px; }
        .header p { margin: 5px 0 0 0; font-size: 11px; color: #666; }

        .profile-table { width: 100%; margin-bottom: 22px; border-collapse: collapse; }
        .profile-table td { padding: 4px 0; }
        .profile-table td.label { width: 15%; font-weight: bold; }
        .profile-table td.colon { width: 2%; }

        .content-title { font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #1F6F5C; border-left: 4px solid #E0A72E; padding-left: 8px; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        .data-table th { background-color: #F3ECDD; font-weight: bold; color: #26312E; }

        .badge { padding: 3px 7px; border-radius: 3px; font-size: 10px; font-weight: bold; color: #fff; }
        .bg-normal   { background-color: #1F6F5C; }
        .bg-kurang   { background-color: #E0A72E; color: #26312E; }
        .bg-lebih    { background-color: #D65A46; }
        .bg-stunting { background-color: #D65A46; }
        .bg-aman     { background-color: #6FA97D; }

        .footer-date { text-align: right; margin-top: 40px; font-size: 11px; color: #777; }
    </style>
</head>
<body>

    <div class="header">
        <h2>MBG Smart Nutrition</h2>
        <p>Sistem Pemantauan Program Makan Bergizi Gratis &amp; Status Gizi Siswa</p>
    </div>

    <div class="content-title">PROFIL SISWA</div>
    <table class="profile-table">
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="colon">:</td>
            <td>{{ $siswa->name }}</td>
        </tr>
        <tr>
            <td class="label">NISN</td>
            <td class="colon">:</td>
            <td>{{ $siswa->nisn }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td class="colon">:</td>
            <td>{{ $siswa->class_name }}</td>
        </tr>
    </table>

    <div class="content-title">RIWAYAT CATATAN PEMERIKSAAN GIZI</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Periksa</th>
                <th>Umur</th>
                <th>Tinggi Badan</th>
                <th>Berat Badan</th>
                <th>BMI</th>
                <th>Status Gizi</th>
                <th>Indikator TB/U</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswa->riwayatAntropometri as $index => $riwayat)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $riwayat->created_at->format('d M Y - H:i') }}</td>
                    <td>{{ $riwayat->age_in_months }} Bulan</td>
                    <td>{{ $riwayat->height_cm }} cm</td>
                    <td>{{ $riwayat->weight_kg }} kg</td>
                    <td><strong>{{ $riwayat->bmi_value }}</strong></td>
                    <td>
                        @if(str_contains($riwayat->gizi_status_conclusion, 'Kurang'))
                            <span class="badge bg-kurang">{{ $riwayat->gizi_status_conclusion }}</span>
                        @elseif(str_contains($riwayat->gizi_status_conclusion, 'Normal') || str_contains($riwayat->gizi_status_conclusion, 'Baik'))
                            <span class="badge bg-normal">{{ $riwayat->gizi_status_conclusion }}</span>
                        @else
                            <span class="badge bg-lebih">{{ $riwayat->gizi_status_conclusion }}</span>
                        @endif
                    </td>
                    <td>
                        @if(in_array($riwayat->stunting_status_conclusion, ['Stunted', 'Severely Stunted', 'Pendek']))
                            <span class="badge bg-stunting">{{ $riwayat->stunting_status_conclusion }}</span>
                        @else
                            <span class="badge bg-aman">{{ $riwayat->stunting_status_conclusion }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Belum ada data pemeriksaan untuk siswa ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-date">
        Dokumen dicetak otomatis oleh Sistem MBG Smart Nutrition pada: {{ date('d F Y - H:i') }} WITA
    </div>

</body>
</html>