<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MBG Smart Nutrition</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --paper: #EFF3EA;
            --paper-2: #E4EADC;
            --forest: #1F3A2E;
            --forest-2: #2F5142;
            --gold: #E3A23B;
            --chili: #C1443C;
            --ink: #1B1F1D;
            --ink-soft: #57635C;
            --card: #FFFFFF;
            --line: #D8DFD1;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--paper);
            color: var(--ink);
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, .display {
            font-family: 'Fraunces', serif;
            font-weight: 600;
            letter-spacing: -0.01em;
            margin: 0;
        }

        .mono { font-family: 'IBM Plex Mono', monospace; }

        a { text-decoration: none; color: inherit; }

        /* ---------- Topbar ---------- */
        .topbar {
            background: var(--forest);
            color: var(--paper);
            padding: 18px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-mark {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Fraunces', serif;
            font-weight: 700;
            color: var(--forest);
            font-size: 16px;
        }
        .brand-name {
            font-family: 'Fraunces', serif;
            font-size: 17px;
            font-weight: 600;
            letter-spacing: 0.01em;
        }
        .brand-sub {
            font-size: 11px;
            color: #B9CBBF;
            margin-top: -2px;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 22px;
        }
        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }
        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--forest-2);
            border: 1px solid #4C6C5B;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
        }
        .logout-btn {
            background: transparent;
            border: 1px solid #4C6C5B;
            color: var(--paper);
            padding: 8px 16px;
            border-radius: 100px;
            font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease;
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.08);
            border-color: #6E8C7A;
        }

        /* ---------- Layout ---------- */
        .wrap {
            max-width: 1180px;
            margin: 0 auto;
            padding: 48px 40px 80px;
        }

        .hero-row {
            display: grid;
            grid-template-columns: 1fr 1.15fr;
            gap: 40px;
            align-items: stretch;
        }

        .eyebrow {
            font-size: 12px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--gold);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .greeting h1 {
            font-size: 34px;
            line-height: 1.15;
        }
        .greeting p {
            margin-top: 12px;
            color: var(--ink-soft);
            font-size: 15px;
            max-width: 40ch;
            line-height: 1.55;
        }

        /* ---------- Plate ring (signature element) ---------- */
        .plate-card {
            background: var(--forest);
            border-radius: 24px;
            padding: 32px;
            color: var(--paper);
            display: flex;
            align-items: center;
            gap: 28px;
            opacity: 0;
            transform: translateY(10px);
            animation: rise 0.6s ease forwards 0.1s;
        }
        .plate-ring-wrap {
            position: relative;
            width: 176px;
            height: 176px;
            flex-shrink: 0;
        }
        .plate-ring-wrap svg { transform: rotate(-90deg); }
        .ring-seg {
            fill: none;
            stroke-width: 16;
            stroke-linecap: butt;
            stroke-dasharray: 502.65;
            stroke-dashoffset: 502.65;
            transition: stroke-dashoffset 1.2s cubic-bezier(.4,0,.2,1);
        }
        .plate-center {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        .plate-center .num {
            font-family: 'Fraunces', serif;
            font-size: 30px;
            font-weight: 600;
        }
        .plate-center .label {
            font-size: 10px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #B9CBBF;
            margin-top: 2px;
        }
        .plate-legend {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .plate-legend .title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #B9CBBF;
            margin-bottom: 4px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px;
        }
        .legend-dot {
            width: 9px; height: 9px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .legend-pct {
            margin-left: auto;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12.5px;
            color: #B9CBBF;
        }

        @keyframes rise {
            to { opacity: 1; transform: translateY(0); }
        }

        /* ---------- Stat cards ---------- */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 32px;
        }
        .stat-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 22px 20px;
            opacity: 0;
            transform: translateY(10px);
            animation: rise 0.6s ease forwards;
        }
        .stat-card:nth-child(1) { animation-delay: 0.15s; }
        .stat-card:nth-child(2) { animation-delay: 0.25s; }
        .stat-card:nth-child(3) { animation-delay: 0.35s; }

        .stat-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            margin-bottom: 14px;
        }
        .stat-value {
            font-family: 'Fraunces', serif;
            font-size: 26px;
            font-weight: 600;
        }
        .stat-label {
            font-size: 13px;
            color: var(--ink-soft);
            margin-top: 2px;
        }
        .stat-trend {
            font-size: 12px;
            margin-top: 10px;
            font-family: 'IBM Plex Mono', monospace;
        }
        .trend-up { color: #2F7A4F; }
        .trend-warn { color: var(--chili); }

        /* ---------- Activity ---------- */
        .section-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin: 56px 0 18px;
        }
        .section-head h2 { font-size: 21px; }
        .section-head span {
            font-size: 13px;
            color: var(--ink-soft);
        }

        .activity-list {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            overflow: hidden;
        }
        .activity-row {
            display: grid;
            grid-template-columns: 44px 1fr auto auto;
            align-items: center;
            gap: 16px;
            padding: 16px 22px;
            border-bottom: 1px solid var(--line);
        }
        .activity-row:last-child { border-bottom: none; }
        .activity-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: var(--paper-2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }
        .activity-title { font-size: 14px; font-weight: 500; }
        .activity-sub { font-size: 12.5px; color: var(--ink-soft); margin-top: 1px; }
        .activity-time {
            font-size: 12px;
            color: var(--ink-soft);
            font-family: 'IBM Plex Mono', monospace;
            white-space: nowrap;
        }
        .badge {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 100px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-good { background: #E4F1E8; color: #2F7A4F; }
        .badge-watch { background: #FBEAE8; color: var(--chili); }

        @media (max-width: 860px) {
            .hero-row { grid-template-columns: 1fr; }
            .stat-grid { grid-template-columns: 1fr; }
            .wrap { padding: 32px 20px 60px; }
            .topbar { padding: 16px 20px; }
            .activity-row { grid-template-columns: 36px 1fr; row-gap: 4px; }
            .activity-time, .activity-row .badge { grid-column: 2; justify-self: start; }
        }

        @media (prefers-reduced-motion: reduce) {
            .plate-card, .stat-card { animation: none; opacity: 1; transform: none; }
            .ring-seg { transition: none; }
        }

        /* ---------- Nav menu (role-based) ---------- */
        .nav-menu {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .nav-menu a {
            font-size: 13.5px;
            padding: 8px 14px;
            border-radius: 100px;
            color: #CBDACE;
            transition: background 0.2s ease, color 0.2s ease;
        }
        .nav-menu a:hover {
            background: rgba(255,255,255,0.08);
            color: #FFFFFF;
        }
        .role-tag {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: var(--gold);
            color: var(--forest);
            padding: 2px 8px;
            border-radius: 100px;
            font-weight: 700;
            margin-left: 8px;
        }
    </style>
</head>
<body>

    <div class="topbar">
        <div class="brand">
            <div class="brand-mark">MBG</div>
            <div>
                <div class="brand-name">Smart Nutrition</div>
                <div class="brand-sub">Program Makan Bergizi</div>
            </div>
        </div>

        <nav class="nav-menu">
            <a href="{{ route('dashboard') }}">Dashboard</a>

            @if(auth()->user()->isGuru())
                <a href="{{ route('siswa.index') }}">Kelola Data Siswa</a>
                <a href="{{ route('siswa.index') }}">Input Pengukuran</a>
            @endif

            @if(auth()->user()->isAdmin())
                {{-- Ganti route('akun.index') ini setelah AkunController dibuat --}}
                <a href="#">Kelola Akun</a>
            @endif
        </nav>

        <div class="topbar-right">
            <div class="user-chip">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                <span>
                    {{ auth()->user()->name ?? 'Pengguna' }}
                    <span class="role-tag">{{ auth()->user()->role ?? '-' }}</span>
                </span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">Keluar</button>
            </form>
        </div>
    </div>

    <div class="wrap">

        <div class="hero-row">
            <div class="greeting">
                <div class="eyebrow">Ringkasan Hari Ini</div>
                <h1>Selamat datang,<br>{{ explode(' ', auth()->user()->name ?? 'Pengguna')[0] }}.</h1>
                <p>Pantau kelengkapan gizi menu makan siang sekolah hari ini berdasarkan pedoman Isi Piringku, langsung dari satu layar.</p>
            </div>

            <div class="plate-card">
                <div class="plate-ring-wrap">
                    <svg viewBox="0 0 176 176" width="176" height="176">
                        <circle cx="88" cy="88" r="80" fill="none" stroke="#2F5142" stroke-width="16" />
                        <circle class="ring-seg" id="seg-karbo" cx="88" cy="88" r="80" stroke="#E3A23B" />
                        <circle class="ring-seg" id="seg-protein" cx="88" cy="88" r="80" stroke="#C1443C" />
                        <circle class="ring-seg" id="seg-sayur" cx="88" cy="88" r="80" stroke="#6FA285" />
                        <circle class="ring-seg" id="seg-buah" cx="88" cy="88" r="80" stroke="#F2D06B" />
                    </svg>
                    <div class="plate-center">
                        <div class="num">94%</div>
                        <div class="label">Terpenuhi</div>
                    </div>
                </div>
                <div class="plate-legend">
                    <div class="title">Isi Piringku &mdash; Menu Hari Ini</div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#E3A23B"></span>
                        Karbohidrat <span class="legend-pct">35%</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#C1443C"></span>
                        Protein <span class="legend-pct">25%</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#6FA285"></span>
                        Sayuran <span class="legend-pct">25%</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#F2D06B"></span>
                        Buah <span class="legend-pct">15%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background:#FBEEDB;">🎓</div>
                <div class="stat-value mono">482</div>
                <div class="stat-label">Siswa terdaftar</div>
                <div class="stat-trend trend-up">↑ 12 siswa baru pekan ini</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#E4F1E8;">🍽️</div>
                <div class="stat-value mono">6</div>
                <div class="stat-label">Menu aktif minggu ini</div>
                <div class="stat-trend trend-up">Semua menu tervalidasi ahli gizi</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#FBEAE8;">⚠️</div>
                <div class="stat-value mono">3</div>
                <div class="stat-label">Siswa perlu perhatian gizi</div>
                <div class="stat-trend trend-warn">Perlu tinjauan minggu ini</div>
            </div>
        </div>

        <div class="section-head">
            <h2>Aktivitas Terbaru</h2>
            <span>Diperbarui otomatis</span>
        </div>

        <div class="activity-list">
            <div class="activity-row">
                <div class="activity-icon">📋</div>
                <div>
                    <div class="activity-title">Menu "Nasi, Ayam Suwir, Tumis Kangkung" ditambahkan</div>
                    <div class="activity-sub">oleh Tim Dapur — Kelas 4A &amp; 4B</div>
                </div>
                <span class="badge badge-good">Tervalidasi</span>
                <div class="activity-time">10:24</div>
            </div>
            <div class="activity-row">
                <div class="activity-icon">🧒</div>
                <div>
                    <div class="activity-title">Data gizi siswa baru diperbarui</div>
                    <div class="activity-sub">Rara A. — status gizi normal</div>
                </div>
                <span class="badge badge-good">Normal</span>
                <div class="activity-time">09:47</div>
            </div>
            <div class="activity-row">
                <div class="activity-icon">📉</div>
                <div>
                    <div class="activity-title">Asupan protein di bawah target</div>
                    <div class="activity-sub">Kelas 2C — 3 siswa terindikasi</div>
                </div>
                <span class="badge badge-watch">Perlu tinjauan</span>
                <div class="activity-time">Kemarin</div>
            </div>
            <div class="activity-row">
                <div class="activity-icon">✅</div>
                <div>
                    <div class="activity-title">Laporan mingguan gizi berhasil diekspor</div>
                    <div class="activity-sub">Format PDF — dikirim ke Dinas Pendidikan</div>
                </div>
                <span class="badge badge-good">Selesai</span>
                <div class="activity-time">Kemarin</div>
            </div>
        </div>

    </div>

    <script>
        // Animasikan ring "Isi Piringku" saat halaman dimuat
        window.addEventListener('DOMContentLoaded', () => {
            const C = 502.65; // keliling lingkaran (2 * pi * r), r = 80
            const segments = [
                { id: 'seg-karbo',   pct: 35 },
                { id: 'seg-protein', pct: 25 },
                { id: 'seg-sayur',   pct: 25 },
                { id: 'seg-buah',    pct: 15 },
            ];

            let cumulative = 0;
            segments.forEach(seg => {
                const el = document.getElementById(seg.id);
                const length = (seg.pct / 100) * C;
                el.style.strokeDasharray = `${length} ${C - length}`;
                el.style.strokeDashoffset = -cumulative;
                cumulative += length;

                // trigger reveal animation dari 0
                requestAnimationFrame(() => {
                    const offsetStart = -cumulative + length;
                    el.style.transition = 'none';
                    const finalOffset = el.style.strokeDashoffset;
                    el.style.strokeDasharray = `0 ${C}`;
                    requestAnimationFrame(() => {
                        el.style.transition = 'stroke-dasharray 1s cubic-bezier(.4,0,.2,1)';
                        el.style.strokeDasharray = `${length} ${C - length}`;
                    });
                });
            });
        });
    </script>

</body>
</html>