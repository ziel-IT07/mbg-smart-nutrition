<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MBG Smart Nutrition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        :root{
            --ink:#26312E;
            --forest:#1F6F5C;
            --forest-dark:#154C40;
            --turmeric:#E0A72E;
            --cream:#FBF7EF;
            --cream-2:#F3ECDD;
            --coral:#D65A46;
            --sage:#6FA97D;
        }
        body{ background:var(--cream); color:var(--ink); font-family:'Plus Jakarta Sans',sans-serif; }
        .font-display{ font-family:'Fraunces',serif; }
        .font-mono{ font-family:'IBM Plex Mono',monospace; }

        .grain{
            background-image: radial-gradient(circle at 1px 1px, rgba(31,111,92,0.08) 1px, transparent 0);
            background-size: 18px 18px;
        }
        .hero-shape{
            background: radial-gradient(120% 140% at 100% 0%, var(--forest) 0%, var(--forest-dark) 55%, #0F332A 100%);
        }
        .sprout::before{
            content:"";
            display:inline-block;
            width:8px; height:8px;
            border-radius:50%;
            margin-right:8px;
        }
        .sprout-normal::before{ background:var(--sage); box-shadow:0 0 0 3px rgba(111,169,125,0.25); }
        .sprout-warn::before{ background:var(--turmeric); box-shadow:0 0 0 3px rgba(224,167,46,0.25); }
        .sprout-danger::before{ background:var(--coral); box-shadow:0 0 0 3px rgba(214,90,70,0.25); }
        .sprout-neutral::before{ background:#B8B0A0; box-shadow:0 0 0 3px rgba(184,176,160,0.25); }

        .field{
            width:100%;
            background:#fff;
            border:1.5px solid #E4DCC8;
            border-radius:12px;
            padding:11px 14px;
            font-size:14.5px;
            transition:border-color .15s ease, box-shadow .15s ease;
        }
        .field:focus{
            outline:none;
            border-color:var(--forest);
            box-shadow:0 0 0 4px rgba(31,111,92,0.12);
        }
        .label-sm{ font-size:12.5px; font-weight:700; letter-spacing:.02em; color:#5A6660; text-transform:uppercase; margin-bottom:6px; display:block; }

        .btn-primary{
            background:var(--forest);
            color:#fff;
            border-radius:12px;
            font-weight:700;
            transition:transform .12s ease, background .15s ease;
        }
        .btn-primary:hover{ background:var(--forest-dark); transform:translateY(-1px); }

        .card-soft{
            background:#fff;
            border-radius:20px;
            border:1px solid #ECE4D2;
            box-shadow: 0 1px 2px rgba(38,49,46,0.04), 0 10px 30px -12px rgba(38,49,46,0.08);
        }

        table.gz thead th{
            font-size:11.5px; text-transform:uppercase; letter-spacing:.05em;
            color:#5A6660; font-weight:700; padding:12px 16px; text-align:left;
            border-bottom:1.5px solid #ECE4D2;
        }
        table.gz tbody td{ padding:14px 16px; border-bottom:1px solid #F2EDE1; font-size:14px; }
        table.gz tbody tr:hover{ background:#FBF9F3; }

        .avatar-initial{
            width:36px; height:36px; border-radius:10px;
            background:linear-gradient(135deg, var(--turmeric), #C98A1D);
            color:#fff; font-weight:700; font-family:'Fraunces',serif;
            display:flex; align-items:center; justify-content:center; font-size:14px;
        }
    </style>
</head>
<body class="min-h-screen">

    <!-- Hero header -->
    <header class="hero-shape text-white">
        <div class="max-w-6xl mx-auto px-6 pt-10 pb-16 relative overflow-hidden">
            <div class="absolute inset-0 grain opacity-30 pointer-events-none"></div>
            <div class="relative flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center font-display text-xl">🌿</div>
                <span class="font-mono text-xs tracking-widest text-white/70 uppercase">Program Makan Bergizi Gratis</span>
            </div>
            <div class="relative">
                <h1 class="font-display text-4xl md:text-5xl font-semibold leading-tight max-w-xl">
                    MBG Smart Nutrition
                </h1>
                <p class="mt-3 text-white/75 max-w-lg text-[15px]">
                    Pemantauan pertumbuhan &amp; status gizi siswa — dari input pemeriksaan hingga indikator stunting, dalam satu tempat.
                </p>
            </div>

            <!-- summary stat strip -->
            @php
                $total = $siswas->count();
                $normal = $siswas->filter(fn($s)=> optional($s->riwayatAntropometri->first())->stunting_status_conclusion && !in_array($s->riwayatAntropometri->first()->stunting_status_conclusion, ['Stunted','Severely Stunted','Pendek']))->count();
                $stunting = $siswas->filter(fn($s)=> optional($s->riwayatAntropometri->first())->stunting_status_conclusion && in_array($s->riwayatAntropometri->first()->stunting_status_conclusion, ['Stunted','Severely Stunted','Pendek']))->count();
                $belum = $total - $normal - $stunting;
            @endphp
            <div class="relative mt-10 grid grid-cols-3 gap-3 max-w-xl">
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 border border-white/10">
                    <div class="font-display text-2xl font-semibold">{{ $total }}</div>
                    <div class="text-xs text-white/70 mt-1">Total Siswa</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 border border-white/10">
                    <div class="font-display text-2xl font-semibold text-emerald-300">{{ $normal }}</div>
                    <div class="text-xs text-white/70 mt-1">Status Aman</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 border border-white/10">
                    <div class="font-display text-2xl font-semibold" style="color:#F2A99A">{{ $stunting }}</div>
                    <div class="text-xs text-white/70 mt-1">Perlu Perhatian</div>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 -mt-8 pb-20 relative z-10">

        @if(session('success'))
            <div class="card-soft mb-6 px-5 py-4 flex items-center gap-3 border-l-4" style="border-left-color:var(--sage)">
                <span class="text-lg">✅</span>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Input form -->
        <section class="card-soft p-6 md:p-8 mb-8">
            <div class="flex items-center gap-2 mb-6">
                <div class="w-1.5 h-6 rounded-full" style="background:var(--turmeric)"></div>
                <h2 class="font-display text-xl font-semibold">Input / Perbarui Pemeriksaan Gizi</h2>
            </div>

            <form action="{{ route('siswa.store') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="school_id" value="1">

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="label-sm">NISN</label>
                        <input type="text" name="nisn" class="field" placeholder="Masukkan NISN Siswa" required>
                        <p class="text-xs text-[#8A8272] mt-1.5">*Data akan tersimpan ke database siswa secara langsung.</p>
                    </div>
                    <div>
                        <label class="label-sm">Nama Siswa</label>
                        <input type="text" name="nama" class="field" placeholder="Masukkan Nama Lengkap" required>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-5">
                    <div>
                        <label class="label-sm">Kelas</label>
                        <input type="text" name="kelas" class="field" placeholder="Contoh: 1-A" required>
                    </div>
                    <div>
                        <label class="label-sm">Tanggal Lahir</label>
                        <input type="date" name="birth_date" id="birth_date" class="field" required>
                        <p id="umur-preview" class="text-xs text-[#8A8272] mt-1.5">Umur akan dihitung otomatis dari tanggal lahir.</p>
                    </div>
                    <div>
                        <label class="label-sm">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="field" required>
                            <option value="">Pilih</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="label-sm">Tinggi Badan (cm)</label>
                        <input type="number" step="0.01" name="tinggi" class="field" placeholder="Contoh: 102.0" required>
                    </div>
                    <div>
                        <label class="label-sm">Berat Badan (kg)</label>
                        <input type="number" step="0.01" name="berat" class="field" placeholder="Contoh: 16.5" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary px-6 py-3 text-sm">
                    Simpan Pemeriksaan →
                </button>
            </form>
        </section>

        <!-- Data table -->
        <section class="card-soft p-6 md:p-8">
            <div class="flex items-center gap-2 mb-6">
                <div class="w-1.5 h-6 rounded-full" style="background:var(--forest)"></div>
                <h2 class="font-display text-xl font-semibold">Data Status Gizi Terkini</h2>
            </div>

            <div class="overflow-x-auto -mx-2">
                <table class="gz w-full min-w-[720px]">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>NISN</th>
                            <th>Kelas</th>
                            <th>BMI Terkini</th>
                            <th>Status Gizi</th>
                            <th>Indikator TB/U</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswas as $siswa)
                            @php
                                $terbaru = $siswa->riwayatAntropometri->first();
                                $initial = strtoupper(substr($siswa->name, 0, 1));
                            @endphp
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar-initial">{{ $initial }}</div>
                                        <span class="font-medium">{{ $siswa->name }}</span>
                                    </div>
                                </td>
                                <td class="font-mono text-[13px] text-[#5A6660]">{{ $siswa->nisn }}</td>
                                <td>{{ $siswa->class_name }}</td>
                                <td class="font-mono">{{ $terbaru ? $terbaru->bmi_value : '—' }}</td>
                                <td>
                                    @if($terbaru)
                                        @if(str_contains($terbaru->gizi_status_conclusion, 'Kurang'))
                                            <span class="sprout sprout-warn text-sm font-medium">{{ $terbaru->gizi_status_conclusion }}</span>
                                        @elseif(str_contains($terbaru->gizi_status_conclusion, 'Normal'))
                                            <span class="sprout sprout-normal text-sm font-medium">{{ $terbaru->gizi_status_conclusion }}</span>
                                        @else
                                            <span class="sprout sprout-danger text-sm font-medium">{{ $terbaru->gizi_status_conclusion }}</span>
                                        @endif
                                    @else
                                        <span class="sprout sprout-neutral text-sm font-medium">Belum ada data</span>
                                    @endif
                                </td>
                                <td>
                                    @if($terbaru)
                                        @if(in_array($terbaru->stunting_status_conclusion, ['Stunted','Severely Stunted','Pendek']))
                                            <span class="sprout sprout-danger text-sm font-medium">{{ $terbaru->stunting_status_conclusion }}</span>
                                        @else
                                            <span class="sprout sprout-normal text-sm font-medium">{{ $terbaru->stunting_status_conclusion }}</span>
                                        @endif
                                    @else
                                        <span class="sprout sprout-neutral text-sm font-medium">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('siswa.show', ['siswa' => $siswa->id]) }}"
                                       class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-lg border transition"
                                       style="color:var(--forest); border-color:#DCEAE4;"
                                       onmouseover="this.style.background='#F3F8F6'" onmouseout="this.style.background='transparent'">
                                        Riwayat →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <script>
        const birthInput = document.getElementById('birth_date');
        const umurPreview = document.getElementById('umur-preview');
        birthInput?.addEventListener('change', function() {
            if (!this.value) {
                umurPreview.textContent = 'Umur akan dihitung otomatis dari tanggal lahir.';
                return;
            }
            const birth = new Date(this.value);
            const now = new Date();
            let months = (now.getFullYear() - birth.getFullYear()) * 12 + (now.getMonth() - birth.getMonth());
            if (now.getDate() < birth.getDate()) months -= 1;
            months = Math.max(months, 0);
            umurPreview.textContent = `Umur saat ini: ${months} bulan`;
        });
    </script>
</body>
</html>