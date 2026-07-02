<!DOCTYPE html>
<html>
<head>
    <title>MBG Smart Nutrition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <h2 class="mb-4">MBG Smart Nutrition</h2>

    <div class="card">
        <div class="card-header fw-bold bg-primary text-white">
            Input / Perbarui Data Pemeriksaan Gizi Siswa
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('siswa.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NISN</label>
                        <input type="text" name="nisn" class="form-control" placeholder="Masukkan NISN Siswa" required>
                        <small class="text-muted">*Jika NISN sudah terdaftar, sistem otomatis menambah riwayat baru tanpa menduplikat nama.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Siswa</label>
                        <input type="text" name="nama" class="form-control" placeholder="Masukkan Nama Lengkap" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kelas</label>
                        <input type="text" name="kelas" class="form-control" placeholder="Contoh: 7-A atau 1" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Umur (Tahun)</label>
                        <input type="number" name="umur" class="form-control" placeholder="Contoh: 12" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tinggi Badan (cm)</label>
                        <input type="number" step="0.01" name="tinggi" class="form-control" placeholder="Contoh: 155.5" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Berat Badan (kg)</label>
                        <input type="number" step="0.01" name="berat" class="form-control" placeholder="Contoh: 45.2" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success px-4">
                    Simpan Pemeriksaan
                </button>
            </form>

            <hr class="my-4">

            <h3 class="mb-3">Data Status Gizi Terkini</h3>

            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>BMI Terkini</th>
                        <th>Status Gizi (BMI)</th>
                        <th>Indikator TB/U (Stunting)</th> <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswas as $siswa)
                        @php
                            $terbaru = $siswa->riwayatGizi->first();
                        @endphp
                    <tr>
                        <td>{{ $siswa->nisn }}</td>
                        <td>{{ $siswa->nama }}</td>
                        <td>{{ $siswa->kelas }}</td>
                        <td>{{ $terbaru ? $terbaru->bmi : '-' }}</td>
                        <td>
                            @if($terbaru)
                                @if($terbaru->status_gizi == 'Kurus')
                                    <span class="badge bg-warning text-dark">Kurus</span>
                                @elseif($terbaru->status_gizi == 'Normal')
                                    <span class="badge bg-success">Normal</span>
                                @elseif($terbaru->status_gizi == 'Gemuk')
                                    <span class="badge bg-info text-dark">Gemuk</span>
                                @else
                                    <span class="badge bg-danger">Obesitas</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">Belum ada data</span>
                            @endif
                        </td>
                        
                        <td>
                            @if($terbaru)
                                @if($terbaru->status_tbu == 'Stunted' || $terbaru->status_tbu == 'Severely Stunted')
                                    <span class="badge bg-danger">⚠️ Terindikasi Stunting</span>
                                @else
                                    <span class="badge bg-success">✅ Normal</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('siswa.show', ['siswa' => $siswa->id]) }}" class="btn btn-sm btn-outline-primary">
                                📊 Lihat Riwayat
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>

</body>
</html>