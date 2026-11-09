<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="{{ asset('img/klickCare-logo.png') }}" type="image/x-icon">
    <title>Konsol Medis Admin - KlickCare Polinema</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/css/navfot.css', 'resources/js/app.js'])
</head>
<body style="background-color: #f1f5f9;">
    @include('components.navbar')
    @include('components.toast')

    <div class="container-fluid px-lg-4 py-4">

        {{-- Admin Console Header --}}
        <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm">
            <div class="row align-items-center justify-content-between g-3">
                <div class="col-md-7">
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle mb-2">Konsol Tenaga Medis & Administrator</span>
                    <h2 class="fw-bold mb-1">Manajemen Operasional Poliklinik</h2>
                    <p class="text-secondary small mb-0">
                        Kelola antrian pasien, panggil nomor antrian ke ruang periksa, serta catat diagnosis dan resep obat secara terintegrasi.
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="badge bg-light text-dark border p-2 text-start">
                        <small class="text-muted d-block">Tanggal Pelayanan:</small>
                        <span class="fw-bold"><i class="bi bi-calendar-check text-primary me-1"></i> {{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4 Clinical KPI Metrics Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="kc-metric-card border-warning">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kc-metric-label text-warning-emphasis">Antrian Menunggu</span>
                        <i class="bi bi-hourglass-split text-warning fs-3"></i>
                    </div>
                    <div class="kc-metric-value text-warning-emphasis">{{ $waitingCount }}</div>
                    <small class="text-muted">Pasien di ruang tunggu</small>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="kc-metric-card border-primary">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kc-metric-label text-primary">Sedang Diperiksa</span>
                        <i class="bi bi-stethoscope text-primary fs-3"></i>
                    </div>
                    <div class="kc-metric-value text-primary">{{ $inProgressCount }}</div>
                    <small class="text-muted">Pasien di ruang dokter</small>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="kc-metric-card border-success">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kc-metric-label text-success">Pemeriksaan Selesai</span>
                        <i class="bi bi-check2-circle text-success fs-3"></i>
                    </div>
                    <div class="kc-metric-value text-success">{{ $completedCount }}</div>
                    <small class="text-muted">Selesai diperiksa hari ini</small>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="kc-metric-card border-secondary">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kc-metric-label text-dark">Total Pasien Terdaftar</span>
                        <i class="bi bi-people text-secondary fs-3"></i>
                    </div>
                    <div class="kc-metric-value text-dark">{{ $totalPatientsCount }}</div>
                    <small class="text-muted">Sivitas akademika aktif</small>
                </div>
            </div>
        </div>

        {{-- Main Queue Table & Operation Console --}}
        <div class="bg-white border rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 border-bottom pb-3 mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Daftar Antrian Kunjungan Pasien Hari Ini</h5>
                    <p class="text-muted small mb-0">Klik tombol tindakan untuk memanggil pasien atau menyelesaikan pemeriksaan.</p>
                </div>

                {{-- Filter Poli --}}
                <form action="{{ route('admin') }}" method="GET" class="d-flex align-items-center gap-2">
                    <select name="poli_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Poliklinik --</option>
                        @foreach ($polies as $p)
                            <option value="{{ $p->id }}" {{ $selectedPoliId == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($selectedPoliId)
                        <a href="{{ route('admin') }}" class="btn btn-outline-secondary btn-sm" title="Reset filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-muted">Kode</th>
                            <th class="small text-muted">Pasien</th>
                            <th class="small text-muted">NIM / NIP</th>
                            <th class="small text-muted">Poli Tujuan</th>
                            <th class="small text-muted">Keluhan Pasien</th>
                            <th class="small text-muted">Status</th>
                            <th class="small text-muted">Catatan Dokter</th>
                            <th class="small text-muted text-center" style="min-width: 180px;">Tindakan Medis</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($todayQueues as $q)
                            <tr class="{{ $q->status === 'in_progress' ? 'table-primary' : '' }}">
                                <td class="mono-num fw-bold fs-6 text-primary">
                                    {{ $q->queue_code }}
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $q->user->name }}</div>
                                    <small class="badge bg-light text-secondary border">{{ $q->user->role_label }}</small>
                                </td>
                                <td class="mono-num small">{{ $q->user->noId }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $q->poli->name }}</div>
                                    <small class="text-muted">{{ $q->poli->room }}</small>
                                </td>
                                <td class="small text-secondary" style="max-width: 220px;">
                                    {{ $q->complaint }}
                                </td>
                                <td>
                                    <span class="badge {{ $q->status_badge_class }}">
                                        {{ $q->status_label }}
                                    </span>
                                </td>
                                <td class="small text-muted" style="max-width: 200px;">
                                    {{ $q->doctor_notes ?? '-' }}
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        @if ($q->status === 'waiting')
                                            {{-- Tombol Panggil --}}
                                            <form action="{{ route('admin.queue.status', $q->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="in_progress">
                                                <button type="submit" class="btn btn-primary btn-sm fw-semibold" title="Panggil ke Ruang Periksa">
                                                    <i class="bi bi-megaphone me-1"></i> Panggil
                                                </button>
                                            </form>
                                        @elseif ($q->status === 'in_progress')
                                            {{-- Tombol Selesaikan dengan Catatan --}}
                                            <button type="button" class="btn btn-success btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#completeModal{{ $q->id }}">
                                                <i class="bi bi-check2-circle me-1"></i> Selesaikan
                                            </button>
                                        @endif

                                        @if ($q->status !== 'completed' && $q->status !== 'cancelled')
                                            <form action="{{ route('admin.queue.status', $q->id) }}" method="POST" onsubmit="return confirm('Batalkan antrian ini?')">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Batalkan Antrian">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Modal Catat Rekam Medis / Resep --}}
                                    @if ($q->status === 'in_progress')
                                        <div class="modal fade" id="completeModal{{ $q->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $q->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.queue.status', $q->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="status" value="completed">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold" id="modalLabel{{ $q->id }}">
                                                                Selesaikan Pemeriksaan {{ $q->queue_code }}
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold">Nama Pasien:</label>
                                                                <input type="text" class="form-control" value="{{ $q->user->name }} ({{ $q->user->noId }})" readonly disabled>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold">Keluhan Pasien:</label>
                                                                <p class="small text-muted p-2 bg-light border rounded">{{ $q->complaint }}</p>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="doctor_notes_{{ $q->id }}" class="form-label small fw-bold">Diagnosis Medis & Resep Obat:</label>
                                                                <textarea class="form-control" id="doctor_notes_{{ $q->id }}" name="doctor_notes" rows="3" 
                                                                          placeholder="Tuliskan hasil diagnosis, tindakan medis, atau resep obat yang diberikan..." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                                            <button type="submit" class="btn btn-success btn-sm fw-bold">Simpan & Selesaikan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted small">
                                    <i class="bi bi-inbox fs-2 text-secondary d-block mb-2"></i>
                                    Tidak ada antrian pasien pada poliklinik yang dipilih untuk hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Master Data Poliklinik & Dokter --}}
        <div class="bg-white border rounded-4 shadow-sm p-4">
            <h5 class="fw-bold mb-3">Status 4 Poliklinik Hari Ini</h5>
            <div class="row g-3">
                @foreach ($polies as $p)
                    <div class="col-md-6 col-lg-3">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <img src="{{ asset($p->icon_path) }}" alt="{{ $p->name }}" height="28" onerror="this.src='{{ asset('img/klickCare-logo.png') }}'">
                                <h6 class="fw-bold mb-0">{{ $p->name }}</h6>
                            </div>
                            <div class="small text-muted mb-1"><i class="bi bi-person-fill text-primary me-1"></i> {{ $p->doctor_name }}</div>
                            <div class="small text-muted mb-2"><i class="bi bi-clock me-1"></i> {{ $p->open_time }} - {{ $p->close_time }} WIB</div>
                            <div class="d-flex justify-content-between align-items-center border-top pt-2 small">
                                <span>Sisa Kuota:</span>
                                <span class="mono-num fw-bold text-primary">{{ $p->remainingQuotaToday() }} / {{ $p->daily_quota }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
