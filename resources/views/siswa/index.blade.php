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
        <div class="card-header">
            Input Data Siswa
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('siswa.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label>NISN</label>
                    <input type="text" name="nisn" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Kelas</label>
                    <input type="text" name="kelas" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Umur</label>
                    <input type="number" name="umur" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Tinggi (cm)</label>
                    <input type="number" step="0.01" name="tinggi" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Berat (kg)</label>
                    <input type="number" step="0.01" name="berat" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>
            </form>

            <hr>

            <h3>Data Siswa</h3>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>BMI</th>
                        <th>Status Gizi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswas as $siswa)
                    <tr>
                        <td>{{ $siswa->nisn }}</td>
                        <td>{{ $siswa->nama }}</td>
                        <td>{{ $siswa->kelas }}</td>
                        <td>{{ $siswa->bmi }}</td>
                        <td>{{ $siswa->status_gizi }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>

</body>
</html>