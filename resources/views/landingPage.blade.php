<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('img/klickCare-logo.png') }}" type="image/x-icon">
    <title>KlickCare - Layanan Poliklinik Terpadu Politeknik Negeri Malang</title>

    {{-- Bootstrap 5.3 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/css/navfot.css', 'resources/js/app.js'])
</head>
<body>
    @include('components.navbar')
    @include('components.toast')

    {{-- Hero Section with Rich Visual Presentation --}}
    <section class="kc-hero-section">
        <div class="container">
            <div class="row align-items-center justify-content-between g-5">
                <div class="col-lg-6">
                    <div class="kc-status-badge">
                        <span class="kc-pulse-dot"></span>
                        <span id="kcLiveStatusText">Poliklinik Buka Hari Ini · 08:00 - 15:30 WIB</span>
                    </div>

                    <h1 class="kc-hero-title">
                        Layanan Kesehatan Kampus <span class="text-gradient-primary">Terpadu & Terpercaya</span>
                    </h1>

                    <p class="kc-hero-lead">
                        Pusat fasilitas kesehatan resmi di lingkungan Politeknik Negeri Malang. Memberikan pelayanan medis bermutu tinggi, konsultasi dokter spesialis, dan sistem antrian digital mandiri untuk seluruh sivitas akademika.
                    </p>

                    <div class="d-flex flex-wrap gap-3 mb-4">
                        @auth
                            <a href="{{ route('user') }}" class="btn btn-kc-primary d-inline-flex align-items-center gap-2">
                                <i class="bi bi-ticket-perforated-fill fs-5"></i>
                                <span>Ambil Nomor Antrian Periksa</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-kc-primary d-inline-flex align-items-center gap-2">
                                <i class="bi bi-ticket-perforated-fill fs-5"></i>
                                <span>Ambil Nomor Antrian Sekarang</span>
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-kc-secondary d-inline-flex align-items-center gap-2">
                                <i class="bi bi-person-plus fs-5"></i>
                                <span>Pendaftaran Pasien Baru</span>
                            </a>
                        @endauth
                    </div>

                    <div class="d-flex align-items-center gap-3 text-secondary small">
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-geo-alt-fill text-primary"></i>
                            <span>Gedung Graha Polinema Lt. 1</span>
                        </div>
                        <span>•</span>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-telephone-fill text-success"></i>
                            <span>(0341) 404424 Ext. 119</span>
                        </div>
                    </div>
                </div>

                {{-- Hero Visual with Real Medical Imagery & Grounded Clinical Badge --}}
                <div class="col-lg-6">
                    <div class="kc-hero-visual-stage position-relative text-center">
                        {{-- Ambient Clinical Soft Glow Halo --}}
                        <div class="kc-hero-glow"></div>

                        {{-- Medical Photography: 100% Full & Uncropped --}}
                        <div class="kc-hero-photo-wrapper">
                            <img src="{{ asset('img/beranda.png') }}" class="kc-hero-photo" alt="Tim Medis Poliklinik Polinema">
                        </div>

                        {{-- Clean Grounded Facility Badge --}}
                        <div class="kc-hero-grounded-bar">
                            <div class="d-flex align-items-center gap-2">
                                <div class="kc-hero-bar-icon">
                                    <i class="bi bi-shield-check text-primary"></i>
                                </div>
                                <div class="text-start">
                                    <strong class="d-block text-dark fw-bold" style="font-size: 0.85rem;">Fasilitas Medis Resmi Kampus</strong>
                                    <span class="text-secondary" style="font-size: 0.75rem;">Tim Dokter Umum, Spesialis Gigi, Mata & Nutrisionis</span>
                                </div>
                            </div>
                            <div class="d-none d-sm-flex align-items-center gap-1 text-success fw-bold small">
                                <span class="kc-pulse-dot"></span>
                                <span style="font-size: 0.75rem;">Siap Melayani</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Operational Metrics Counter Strip with Animated Count-Up --}}
            <div class="row g-3 mt-4 pt-3" id="kcMetricsStrip">
                <div class="col-sm-6 col-lg-3">
                    <div class="kc-metric-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="kc-metric-label">Antrian Menunggu</span>
                            <i class="bi bi-hourglass-split text-warning fs-3"></i>
                        </div>
                        <div class="kc-metric-value text-warning-emphasis" data-counter-value="{{ $waitingToday ?? 0 }}">{{ $waitingToday ?? 0 }}</div>
                        <small class="text-muted">Pasien dalam antrian</small>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="kc-metric-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="kc-metric-label">Sedang Diperiksa</span>
                            <i class="bi bi-stethoscope text-primary fs-3"></i>
                        </div>
                        <div class="kc-metric-value text-primary" data-counter-value="{{ $inProgressToday ?? 0 }}">{{ $inProgressToday ?? 0 }}</div>
                        <small class="text-muted">Di ruang dokter jaga</small>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="kc-metric-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="kc-metric-label">Pasien Selesai</span>
                            <i class="bi bi-check2-circle text-success fs-3"></i>
                        </div>
                        <div class="kc-metric-value text-success" data-counter-value="{{ $completedToday ?? 0 }}">{{ $completedToday ?? 0 }}</div>
                        <small class="text-muted">Pemeriksaan hari ini</small>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="kc-metric-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="kc-metric-label">Dokter Spesialis</span>
                            <i class="bi bi-hospital text-info fs-3"></i>
                        </div>
                        <div class="kc-metric-value text-dark" data-counter-value="{{ $activeDoctorsCount ?? 4 }}">{{ $activeDoctorsCount ?? 4 }}</div>
                        <small class="text-muted">Siap melayani hari ini</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 4 Poliklinik --}}
    <section class="py-5" id="layanan-poli">
        <div class="container py-3">
            <div class="text-center max-w-2xl mx-auto mb-4">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 mb-2 fw-semibold">Unit Pelayanan Medis</span>
                <h2 class="fw-bold">Layanan 4 Poliklinik Polinema</h2>
                <p class="text-secondary">Setiap poli didukung oleh tenaga dokter spesialis bersertifikasi serta peralatan klinis lengkap untuk melayani mahasiswa dan staf.</p>
            </div>

            {{-- Interactive Category Filter Tabs --}}
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-3" id="poliFilterTabs">
                <button type="button" class="btn btn-sm kc-filter-pill active" data-filter="all">
                    <i class="bi bi-grid-fill me-1"></i> Semua Poli ({{ $polies->count() }})
                </button>
                @foreach ($polies as $p)
                    <button type="button" class="btn btn-sm kc-filter-pill" data-filter="{{ $p->slug }}" data-poli-name="{{ $p->name }}">
                        <img src="{{ asset($p->icon_path) }}" height="15" class="me-1" onerror="this.style.display='none'">
                        {{ $p->name }}
                    </button>
                @endforeach
            </div>

            <div id="poliFilterNotice" class="text-center mb-4 d-none">
                <span class="badge bg-light text-secondary border px-3 py-2 fw-medium shadow-xs">
                    <i class="bi bi-info-circle text-primary me-1"></i>
                    Fokus Layanan: <strong class="text-dark" id="poliFilterNoticeName">Layanan</strong> &bull;
                    <button type="button" class="btn btn-link btn-sm p-0 ms-1 fw-bold text-primary text-decoration-none" id="btnResetPoliFilter">
                        Tampilkan Semua Poli ({{ $polies->count() }}) &rarr;
                    </button>
                </span>
            </div>

            <div class="row g-4 justify-content-center" id="poliCardsRow">
                @foreach ($polies as $poli)
                    <div class="col-md-6 col-lg-3 poli-card-col" data-poli-slug="{{ $poli->slug }}">
                        <div class="kc-poli-card">
                            <div class="kc-poli-card-top">
                                <div class="kc-poli-header">
                                    <div class="kc-poli-icon-wrapper">
                                        <img src="{{ asset($poli->icon_path) }}" alt="{{ $poli->name }}" onerror="this.src='{{ asset('img/klickCare-logo.png') }}'">
                                    </div>
                                    <div class="kc-poli-header-info">
                                        <h5 class="kc-poli-title">{{ $poli->name }}</h5>
                                        <span class="badge bg-light text-primary border fw-semibold">
                                            Kode: {{ $poli->code_prefix }}
                                        </span>
                                    </div>
                                </div>

                                <p class="kc-poli-desc" title="{{ $poli->description }}">
                                    {{ $poli->description }}
                                </p>
                            </div>

                            <div class="kc-poli-card-bottom">
                                <div class="kc-poli-meta">
                                    <div class="d-flex align-items-center gap-1 mb-1 kc-poli-doc-row" title="{{ $poli->doctor_name }}">
                                        <i class="bi bi-person-badge-fill text-primary flex-shrink-0"></i>
                                        <span class="fw-semibold text-dark text-truncate">{{ $poli->doctor_name }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 text-secondary mb-1">
                                        <i class="bi bi-clock-fill text-muted flex-shrink-0"></i>
                                        <span>{{ $poli->open_time }} - {{ $poli->close_time }} WIB</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-2">
                                        <span class="text-secondary small">Sisa Kuota:</span>
                                        <span class="mono-num fw-bold text-success fs-6">{{ $poli->remainingQuotaToday() }} / {{ $poli->daily_quota }}</span>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <a href="{{ route('poli.detail', $poli->slug) }}" class="btn btn-outline-primary btn-sm w-100 fw-semibold rounded-3 d-flex align-items-center justify-content-center gap-1">
                                        <span>Lihat Jadwal & Antrian</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Section Alur Periksa --}}
    <section class="py-5 bg-white border-top border-bottom" id="alur-periksa">
        <div class="container py-3">
            <div class="text-center max-w-2xl mx-auto mb-5">
                <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 mb-2 fw-semibold">Panduan Pasien</span>
                <h2 class="fw-bold">Alur Pendaftaran & Pemeriksaan Medis</h2>
                <p class="text-secondary">Tiga langkah mudah untuk mendapatkan pelayanan kesehatan profesional di Poliklinik Politeknik Negeri Malang.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="kc-step-box">
                        <div class="kc-step-num">01</div>
                        <h5 class="fw-bold mb-2">Daftar Antrian Online</h5>
                        <p class="text-secondary small mb-0">
                            Masuk dengan akun civitas akademika (NIM/NIP), pilih Poliklinik tujuan, tentukan tanggal kunjungan, dan tuliskan keluhan kesehatan utama Anda.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kc-step-box">
                        <div class="kc-step-num">02</div>
                        <h5 class="fw-bold mb-2">Datang & Pantau Antrian</h5>
                        <p class="text-secondary small mb-0">
                            Kunjungi Poliklinik di Gedung Graha Polinema Lt. 1. Pantau panggilan nomor antrian Anda secara langsung melalui monitor atau ponsel.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kc-step-box">
                        <div class="kc-step-num">03</div>
                        <h5 class="fw-bold mb-2">Pemeriksaan & Resep Obat</h5>
                        <p class="text-secondary small mb-0">
                            Konsultasikan keluhan medis dengan dokter jaga. Terima resep obat, surat rujukan, atau surat keterangan sakit resmi jika diperlukan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- High-Contrast Legible Footer --}}
    <footer class="kc-footer">
        <div class="container">
            <div class="row g-4 justify-content-between">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="{{ asset('img/klickCare-logo.png') }}" alt="Logo" height="40">
                        <h5 class="mb-0 fw-bold">KlickCare Polinema</h5>
                    </div>
                    <p class="small">
                        Unit Pelayanan Kesehatan Resmi Politeknik Negeri Malang. Berdedikasi menjaga kesehatan sivitas akademika demi terciptanya generasi unggul dan berprestasi.
                    </p>
                    <div class="d-flex gap-3 mt-3">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Fasilitas Kampus</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Layanan Gratis Sivitas</span>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5>Jam Pelayanan Medis</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><strong>Senin - Kamis:</strong> 08:00 - 15:30 WIB</li>
                        <li class="mb-2"><strong>Jumat:</strong> 08:00 - 11:30 & 13:00 - 15:00 WIB</li>
                        <li class="mb-2"><strong>Sabtu - Minggu:</strong> Tutup (Pelayanan Darurat Rujukan)</li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h5>Lokasi & Kontak Darurat</h5>
                    <p class="small mb-2">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i> Gedung Graha Polinema Lantai 1, Jl. Soekarno Hatta No. 9, Kota Malang, Jawa Timur 65141
                    </p>
                    <p class="small mb-2">
                        <i class="bi bi-telephone-fill text-success me-2"></i> Telepon: (0341) 404424 / 404425
                    </p>
                    <p class="small">
                        <i class="bi bi-envelope-fill text-info me-2"></i> Email: <a href="mailto:poliklinik@polinema.ac.id">poliklinik@polinema.ac.id</a>
                    </p>
                </div>
            </div>

            <div class="kc-footer-bottom text-center">
                <p class="mb-0">© {{ date('Y') }} KlickCare - Poliklinik Politeknik Negeri Malang. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    {{-- Bootstrap JS Bundle (BS 5.3) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
