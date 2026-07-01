<!DOCTYPE html>
<html>
<head>
    <title>Rapor Gizi Siswa</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #222; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; font-size: 11px; color: #666; }
        .profile-table { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .profile-table td { padding: 4px 0; }
        .profile-table td.label { width: 15%; font-weight: bold; }
        .profile-table td.colon { width: 2%; }
        .content-title { font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #0d6efd; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        .data-table th { background-color: #f2f2f2; font-weight: bold; }
        .badge { padding: 3px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; color: #fff; }
        .bg-normal { background-color: #28a745; }
        .bg-kurus { background-color: #ffc107; color: #333; }
        .bg-gemuk { background-color: #17a2b8; }
        .bg-obesitas { background-color: #dc3545; }
        .footer-date { text-align: right; margin-top: 40px; font-size: 11px; color: #777; }
    </style>
</head>
<body>

    <div class="header">
        <h2>MBG Smart Nutrition</h2>
        <p>Sistem Pemantauan Program Makan Bergizi Gratis & Status Gizi Siswa</p>
    </div>

    <div class="content-title">PROFIL SISWA</div>
    <table class="profile-table">
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="colon">:</td>
            <td>{{ $siswa->nama }}</td>
        </tr>
        <tr>
            <td class="label">NISN</td>
            <td class="colon">:</td>
            <td>{{ $siswa->nisn }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td class="colon">:</td>
            <td>{{ $siswa->kelas }}</td>
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
            </tr>
        </thead>
        <tbody>
            @foreach($siswa->riwayatGizi as $index => $riwayat)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $riwayat->created_at->format('d M Y - H:i') }}</td>
                    <td>{{ $riwayat->umur }} Tahun</td>
                    <td>{{ $riwayat->tinggi }} cm</td>
                    <td>{{ $riwayat->berat }} kg</td>
                    <td><strong>{{ $riwayat->bmi }}</strong></td>
                    <td>
                        @if($riwayat->status_gizi == 'Normal')
                            <span class="badge bg-normal">Normal</span>
                        @elseif($riwayat->status_gizi == 'Kurus')
                            <span class="badge bg-kurus">Kurus</span>
                        @elseif($riwayat->status_gizi == 'Gemuk')
                            <span class="badge bg-gemuk">Gemuk</span>
                        @else
                            <span class="badge bg-obesitas">Obesitas</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-date">
        Dokumen dicetak otomatis oleh Sistem MBG Smart Nutrition pada: {{ date('d F Y - H:i') }} WITA
    </div>

</body>
</html>