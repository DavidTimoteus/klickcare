<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('img/klickCare-logo.png') }}" type="image/x-icon">
    <title>Portal Pasien - KlickCare Polinema</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/css/navfot.css', 'resources/js/app.js'])
</head>
<body style="background-color: #f8fafc;">
    @include('components.navbar')

    <div class="container py-4">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Patient Header Banner --}}
        <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm">
            <div class="row align-items-center justify-content-between g-3">
                <div class="col-md-7">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-2">Portal Sivitas Akademika</span>
                    <h2 class="fw-bold mb-1">Halo, {{ $user->name }}</h2>
                    <p class="text-secondary small mb-0">
                        <span class="me-3"><i class="bi bi-card-heading text-primary me-1"></i> NIM/NIP: <strong class="mono-num">{{ $user->noId }}</strong></span>
                        <span class="me-3"><i class="bi bi-envelope text-primary me-1"></i> {{ $user->email }}</span>
                        <span><i class="bi bi-award text-primary me-1"></i> {{ $user->role_label }}</span>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="badge bg-light text-dark border p-2 text-start">
                        <small class="text-muted d-block">Tanggal Hari Ini:</small>
                        <span class="fw-bold"><i class="bi bi-calendar-event text-primary me-1"></i> {{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            {{-- Left Column: Active Queue Card --}}
            <div class="col-lg-5">
                <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                        <h5 class="fw-bold mb-0">Tiket Antrian Hari Ini</h5>
                        <i class="bi bi-ticket-detailed text-primary fs-4"></i>
                    </div>

                    @if ($activeQueue)
                        <div class="text-center py-3 bg-light rounded-4 border mb-3">
                            <span class="badge {{ $activeQueue->status_badge_class }} text-uppercase mb-2">
                                {{ $activeQueue->status_label }}
                            </span>
                            <div class="mono-num fw-bold text-primary" style="font-size: 3.5rem; line-height: 1;">
                                {{ $activeQueue->queue_code }}
                            </div>
                            <div class="text-dark fw-bold fs-5 mt-2">{{ $activeQueue->poli->name }}</div>
                            <div class="text-muted small">{{ $activeQueue->poli->room }}</div>
                        </div>

                        <div class="list-group list-group-flush small border-bottom mb-3">
                            <div class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Dokter Jaga:</span>
                                <span class="fw-bold">{{ $activeQueue->poli->doctor_name }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Jam Layanan:</span>
                                <span class="fw-semibold">{{ $activeQueue->poli->open_time }} - {{ $activeQueue->poli->close_time }} WIB</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Keluhan:</span>
                                <span class="text-end text-dark" style="max-width: 60%;">{{ $activeQueue->complaint }}</span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('poli.detail', $activeQueue->poli->slug) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-display me-1"></i> Pantau Antrian
                            </a>
                            @if ($activeQueue->status === 'waiting')
                                <form action="{{ route('queue.cancel', $activeQueue->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan antrian ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-x-circle me-1"></i> Batal
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <div class="fs-1 text-secondary mb-2"><i class="bi bi-ticket-perforated"></i></div>
                            <h6 class="fw-bold text-dark">Belum Ada Antrian Aktif</h6>
                            <p class="small text-muted mb-0">Silakan pilih poliklinik pada formulir di samping untuk mengambil nomor antrian periksa hari ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Column: Form Ambil Antrian --}}
            <div class="col-lg-7">
                <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
                    <div class="border-bottom pb-3 mb-3">
                        <h5 class="fw-bold mb-1">Pendaftaran Kunjungan & Antrian Baru</h5>
                        <p class="text-muted small mb-0">Pilih poli tujuan dan tuliskan keluhan medis Anda secara ringkas.</p>
                    </div>

                    <form action="{{ route('queue.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="poli_id" class="form-label small fw-bold text-secondary">Pilih Poliklinik</label>
                            <select class="form-select @error('poli_id') is-invalid @enderror" id="poli_id" name="poli_id" required>
                                <option value="" disabled selected>-- Pilih Layanan Poli --</option>
                                @foreach ($polies as $p)
                                    <option value="{{ $p->id }}" {{ old('poli_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} (Dokter: {{ $p->doctor_name }}) — Sisa Kuota: {{ $p->remainingQuotaToday() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="visit_date" class="form-label small fw-bold text-secondary">Tanggal Kunjungan</label>
                            <input type="date" class="form-control" id="visit_date" name="visit_date" 
                                   value="{{ old('visit_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="complaint" class="form-label small fw-bold text-secondary">Keluhan Utama / Alasan Kunjungan</label>
                            <textarea class="form-control @error('complaint') is-invalid @enderror" id="complaint" name="complaint" rows="3" 
                                      placeholder="Jelaskan secara singkat gejala atau keluhan kesehatan yang dirasakan (contoh: batuk pilek 3 hari, sakit gigi berlubang, konsultasi gizi)" required>{{ old('complaint') }}</textarea>
                            <div class="form-text small">Informasi ini akan langsung diterima oleh dokter pemeriksa di poliklinik.</div>
                        </div>

                        <button type="submit" class="btn btn-kc-primary w-100 py-2 fw-bold shadow-sm">
                            <i class="bi bi-send-check me-1"></i> Terbitkan Nomor Antrian
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Riwayat Kunjungan Pasien --}}
        <div class="bg-white border rounded-4 p-4 shadow-sm">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Riwayat Pemeriksaan Medis Anda</h5>
                    <p class="text-muted small mb-0">Catatan kunjungan, status pemeriksaan, dan resep dokter terdahulu.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-muted">Tanggal Kunjungan</th>
                            <th class="small text-muted">Nomor Tiket</th>
                            <th class="small text-muted">Poliklinik & Dokter</th>
                            <th class="small text-muted">Keluhan Awal</th>
                            <th class="small text-muted">Status</th>
                            <th class="small text-muted">Catatan Medis / Resep</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($historyQueues as $hist)
                            <tr>
                                <td class="small">{{ $hist->visit_date->translatedFormat('d M Y') }}</td>
                                <td class="mono-num fw-bold text-primary">{{ $hist->queue_code }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $hist->poli->name }}</div>
                                    <small class="text-muted">{{ $hist->poli->doctor_name }}</small>
                                </td>
                                <td class="small text-secondary" style="max-width: 250px;">{{ $hist->complaint }}</td>
                                <td>
                                    <span class="badge {{ $hist->status_badge_class }}">
                                        {{ $hist->status_label }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    {{ $hist->doctor_notes ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted small">
                                    Belum ada riwayat kunjungan medis yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $historyQueues->links() }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
