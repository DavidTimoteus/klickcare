<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('img/klickCare-logo.png') }}" type="image/x-icon">
    <title>{{ $poli->name }} - KlickCare Polinema</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/css/navfot.css', 'resources/js/app.js'])
</head>
<body>
    @include('components.navbar')
    @include('components.toast')

    <div class="container py-5">
        <div class="mb-4">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
            </a>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="bg-white border rounded-4 p-4 shadow-sm">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="kc-poli-icon-wrapper" style="width: 64px; height: 64px;">
                            <img src="{{ asset($poli->icon_path) }}" alt="{{ $poli->name }}" style="width: 40px; height: 40px;" onerror="this.src='{{ asset('img/klickCare-logo.png') }}'">
                        </div>
                        <div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Poliklinik Spesialis</span>
                            <h3 class="fw-bold mb-0 mt-1">{{ $poli->name }}</h3>
                        </div>
                    </div>

                    <p class="text-secondary small mb-4">
                        {{ $poli->description }}
                    </p>

                    <div class="list-group list-group-flush border-top border-bottom mb-4">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted"><i class="bi bi-person-badge text-primary me-2"></i>Dokter Bertugas:</span>
                            <span class="fw-bold text-dark">{{ $poli->doctor_name }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted"><i class="bi bi-geo-alt text-primary me-2"></i>Ruangan:</span>
                            <span class="fw-semibold text-dark">{{ $poli->room }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted"><i class="bi bi-clock text-primary me-2"></i>Jam Praktik:</span>
                            <span class="fw-semibold text-dark">{{ $poli->open_time }} - {{ $poli->close_time }} WIB</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-muted"><i class="bi bi-people text-primary me-2"></i>Sisa Kuota Hari Ini:</span>
                            <span class="mono-num fw-bold text-success fs-6">{{ $poli->remainingQuotaToday() }} / {{ $poli->daily_quota }}</span>
                        </div>
                    </div>

                    @auth
                        @if (!Auth::user()->isAdmin())
                            <a href="{{ route('user') }}" class="btn btn-kc-primary w-100 fw-semibold shadow-sm">
                                <i class="bi bi-ticket-perforated-fill me-1"></i> Buka Portal & Ambil Antrian
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-kc-primary w-100 fw-semibold shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk untuk Ambil Antrian
                        </a>
                    @endauth
                </div>
            </div>

            <div class="col-lg-7">
                <div class="bg-white border rounded-4 p-4 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                        <div>
                            <h5 class="fw-bold mb-1">Status Panggilan Antrian Hari Ini</h5>
                            <span class="text-muted small">{{ now()->translatedFormat('l, d F Y') }}</span>
                        </div>
                        <div class="badge bg-light text-dark border p-2">
                            Kode Awalan: <span class="mono-num fw-bold text-primary">{{ $poli->code_prefix }}</span>
                        </div>
                    </div>

                    @if ($currentCalling)
                        <div class="alert alert-primary d-flex align-items-center gap-3 p-3 rounded-3 mb-4" role="alert">
                            <div class="fs-1 text-primary"><i class="bi bi-megaphone-fill"></i></div>
                            <div>
                                <span class="badge bg-primary text-white text-uppercase">Sedang Dipanggil</span>
                                <h2 class="mono-num fw-bold mb-0 text-primary">{{ $currentCalling->queue_code }}</h2>
                                <small class="text-muted">Pasien dipersilakan masuk ke ruang {{ $poli->room }}.</small>
                            </div>
                        </div>
                    @else
                        <div class="p-3 bg-light rounded-3 text-center border mb-4">
                            <span class="text-muted small">Saat ini belum ada panggilan pasien yang sedang berlangsung.</span>
                        </div>
                    @endif

                    <h6 class="fw-bold text-secondary mb-3">Daftar Antrian Menunggu</h6>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="small text-muted">No</th>
                                    <th class="small text-muted">Kode Antrian</th>
                                    <th class="small text-muted">Status</th>
                                    <th class="small text-muted">Waktu Daftar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activeQueues as $q)
                                    <tr>
                                        <td class="mono-num text-muted">{{ $loop->iteration }}</td>
                                        <td class="mono-num fw-bold fs-6 text-primary">{{ $q->queue_code }}</td>
                                        <td>
                                            <span class="badge {{ $q->status_badge_class }}">
                                                {{ $q->status_label }}
                                            </span>
                                        </td>
                                        <td class="small text-muted">{{ $q->created_at->format('H:i') }} WIB</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">
                                            Belum ada pasien yang mengantri di {{ $poli->name }} hari ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
