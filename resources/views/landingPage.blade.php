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

    {{-- Flash Notifications --}}
    @if (session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if (session('info'))
        <div class="container mt-3">
            <div class="alert alert-info alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    {{-- Hero Section --}}
    <section class="kc-hero-section">
        <div class="container">
            <div class="row align-items-center justify-content-between g-5">
                <div class="col-lg-6">
                    <div class="kc-status-badge">
                        <span class="kc-pulse-dot"></span>
                        <span>Poliklinik Buka Hari Ini · 08:00 - 15:30 WIB</span>
                    </div>

                    <h1 class="kc-hero-title">
                        Layanan Kesehatan Kampus Terpadu & Terpercaya
                    </h1>

                    <p class="kc-hero-lead">
                        Pusat fasilitas kesehatan resmi di lingkungan Politeknik Negeri Malang. Memberikan pelayanan medis bermutu tinggi, konsultasi dokter spesialis, dan sistem antrian digital mandiri untuk seluruh sivitas akademika.
                    </p>

                    <div class="d-flex flex-wrap gap-3 mb-4">
                        @auth
                            <a href="{{ route('user') }}" class="btn btn-kc-primary d-inline-flex align-items-center gap-2 shadow-sm">
                                <i class="bi bi-ticket-perforated-fill"></i>
                                <span>Ambil Nomor Antrian Periksa</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-kc-primary d-inline-flex align-items-center gap-2 shadow-sm">
                                <i class="bi bi-ticket-perforated-fill"></i>
                                <span>Ambil Nomor Antrian Sekarang</span>
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-kc-secondary d-inline-flex align-items-center gap-2">
                                <i class="bi bi-person-plus"></i>
                                <span>Pendaftaran Pasien Baru</span>
                            </a>
                        @endauth
                    </div>

                    <div class="d-flex align-items-center gap-3 text-muted small">
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-geo-alt text-primary"></i>
                            <span>Gedung Graha Polinema Lt. 1</span>
                        </div>
                        <span>•</span>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-telephone text-primary"></i>
                            <span>(0341) 404424 Ext. 119</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    {{-- Quick Operational Status Monitor --}}
                    <div class="bg-white p-4 rounded-4 border shadow-sm">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-activity text-primary fs-4"></i>
                                <h6 class="mb-0 fw-bold">Status Antrian Hari Ini</h6>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                {{ now()->translatedFormat('l, d F Y') }}
                            </span>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3 text-center border">
                                    <div class="mono-num fs-2 fw-bold text-primary">{{ $waitingToday ?? 0 }}</div>
                                    <div class="text-muted small fw-semibold text-uppercase">Antrian Menunggu</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3 text-center border">
                                    <div class="mono-num fs-2 fw-bold text-success">{{ $inProgressToday ?? 0 }}</div>
                                    <div class="text-muted small fw-semibold text-uppercase">Sedang Diperiksa</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3 text-center border">
                                    <div class="mono-num fs-2 fw-bold text-secondary">{{ $completedToday ?? 0 }}</div>
                                    <div class="text-muted small fw-semibold text-uppercase">Pasien Selesai</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3 text-center border">
                                    <div class="mono-num fs-2 fw-bold text-dark">{{ $activeDoctorsCount ?? 4 }}</div>
                                    <div class="text-muted small fw-semibold text-uppercase">Poli Aktif Hari Ini</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-top text-center">
                            <small class="text-muted">
                                <i class="bi bi-shield-check text-success me-1"></i> Pelayanan terintegrasi dengan Nomor Induk Mahasiswa (NIM) & NIP Polinema
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 4 Poliklinik --}}
    <section class="py-5" id="layanan-poli">
        <div class="container py-3">
            <div class="text-center max-w-2xl mx-auto mb-5">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 mb-2">Unit Pelayanan Medis</span>
                <h2 class="fw-bold">Layanan Poliklinik Polinema</h2>
                <p class="text-muted">Setiap poli didukung oleh tenaga medis profesional dan peralatan klinis yang memadai untuk melayani mahasiswa dan staf.</p>
            </div>

            <div class="row g-4">
                @foreach ($polies as $poli)
                    <div class="col-md-6 col-lg-3">
                        <div class="kc-poli-card">
                            <div>
                                <div class="kc-poli-header">
                                    <div class="kc-poli-icon-wrapper">
                                        <img src="{{ asset($poli->icon_path) }}" alt="{{ $poli->name }}" onerror="this.src='{{ asset('img/klickCare-logo.png') }}'">
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">{{ $poli->name }}</h5>
                                        <div class="badge bg-light text-dark border">
                                            Kode: {{ $poli->code_prefix }}
                                        </div>
                                    </div>
                                </div>

                                <p class="text-muted small mb-3">
                                    {{ Str::limit($poli->description, 110) }}
                                </p>
                            </div>

                            <div>
                                <div class="kc-poli-meta">
                                    <div class="d-flex align-items-center gap-1 mb-1">
                                        <i class="bi bi-person-badge text-primary"></i>
                                        <span class="fw-semibold">{{ $poli->doctor_name }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 text-muted mb-1">
                                        <i class="bi bi-clock"></i>
                                        <span>{{ $poli->open_time }} - {{ $poli->close_time }} WIB</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-2">
                                        <span class="text-muted">Sisa Kuota:</span>
                                        <span class="mono-num fw-bold text-primary">{{ $poli->remainingQuotaToday() }} / {{ $poli->daily_quota }}</span>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <a href="{{ route('poli.detail', $poli->slug) }}" class="btn btn-outline-primary btn-sm w-100 fw-semibold rounded-3">
                                        Lihat Jadwal & Antrian <i class="bi bi-arrow-right ms-1"></i>
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
                <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 mb-2">Panduan Pengunjung</span>
                <h2 class="fw-bold">Alur Pendaftaran & Pemeriksaan Medis</h2>
                <p class="text-muted">Tiga langkah mudah untuk mendapatkan pelayanan kesehatan di Poliklinik Politeknik Negeri Malang.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="kc-step-box">
                        <div class="kc-step-num">01</div>
                        <h5 class="fw-bold mb-2">Daftar Antrian Online</h5>
                        <p class="text-muted small mb-0">
                            Masuk dengan akun civitas akademika (NIM/NIP), pilih Poliklinik tujuan, tentukan tanggal kunjungan, dan tuliskan keluhan kesehatan utama Anda.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kc-step-box">
                        <div class="kc-step-num">02</div>
                        <h5 class="fw-bold mb-2">Datang & Pantau Antrian</h5>
                        <p class="text-muted small mb-0">
                            Kunjungi Poliklinik di Gedung Graha Polinema Lt. 1. Pantau panggilan nomor antrian Anda secara langsung melalui monitor atau ponsel.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kc-step-box">
                        <div class="kc-step-num">03</div>
                        <h5 class="fw-bold mb-2">Pemeriksaan & Resep Obat</h5>
                        <p class="text-muted small mb-0">
                            Konsultasikan keluhan medis dengan dokter jaga. Terima resep obat, surat rujukan, atau surat izin sakit resmi jika diperlukan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="kc-footer">
        <div class="container">
            <div class="row g-4 justify-content-between">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="{{ asset('img/klickCare-logo.png') }}" alt="Logo" height="36">
                        <h5 class="text-white mb-0 fw-bold">KlickCare Polinema</h5>
                    </div>
                    <p class="text-muted small">
                        Unit Pelayanan Kesehatan Resmi Politeknik Negeri Malang. Berdedikasi menjaga kesehatan sivitas akademika demi terciptanya generasi unggul dan berprestasi.
                    </p>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5>Jam Pelayanan Medis</h5>
                    <ul class="list-unstyled text-muted small">
                        <li class="mb-2"><strong>Senin - Kamis:</strong> 08:00 - 15:30 WIB</li>
                        <li class="mb-2"><strong>Jumat:</strong> 08:00 - 11:30 & 13:00 - 15:00 WIB</li>
                        <li class="mb-2"><strong>Sabtu - Minggu:</strong> Tutup (Pelayanan Darurat Rujukan)</li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h5>Lokasi & Kontak</h5>
                    <p class="text-muted small mb-1">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> Gedung Graha Polinema Lantai 1, Jl. Soekarno Hatta No. 9, Kota Malang, Jawa Timur 65141
                    </p>
                    <p class="text-muted small mb-1">
                        <i class="bi bi-telephone-fill text-success me-1"></i> (0341) 404424 / 404425
                    </p>
                    <p class="text-muted small">
                        <i class="bi bi-envelope-fill text-primary me-1"></i> poliklinik@polinema.ac.id
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
