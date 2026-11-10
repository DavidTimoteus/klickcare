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
                <div class="col-md-5 text-md-end">
                    <div class="d-flex flex-wrap justify-content-md-end align-items-center gap-2 mb-2">
                        <a href="{{ route('admin.pharmacy') }}" class="btn btn-outline-primary btn-sm rounded-3 fw-semibold position-relative">
                            <i class="bi bi-capsule me-1"></i> Stok Farmasi
                            @if ($lowStockCount > 0)
                                <span class="badge bg-danger ms-1">{{ $lowStockCount }} menipis</span>
                            @endif
                        </a>
                        <div class="badge bg-light text-dark border p-2 text-start">
                            <small class="text-muted d-block">Tanggal Pelayanan:</small>
                            <span class="fw-bold"><i class="bi bi-calendar-check text-primary me-1"></i> {{ now()->translatedFormat('l, d F Y') }}</span>
                        </div>
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
                            <th class="small text-muted">Pasien & No. RM</th>
                            <th class="small text-muted">NIM / NIP</th>
                            <th class="small text-muted">Poli Tujuan</th>
                            <th class="small text-muted">Keluhan Utama</th>
                            <th class="small text-muted">Status</th>
                            <th class="small text-muted">Catatan Dokter</th>
                            <th class="small text-muted text-center" style="min-width: 190px;">Tindakan Medis</th>
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
                                    <div class="d-flex flex-wrap align-items-center gap-1 mt-1">
                                        <span class="badge bg-light text-dark border mono-num" style="font-size: 0.7rem;" title="Nomor Rekam Medis Permanen">
                                            <i class="bi bi-file-medical text-primary me-1"></i>{{ $q->user->formatted_medical_record_number }}
                                        </span>
                                        @if ($q->user->allergies && $q->user->allergies !== 'Tidak ada riwayat alergi obat')
                                            <span class="badge bg-danger text-white" style="font-size: 0.675rem;" title="Alergi Obat: {{ $q->user->allergies }}">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Alergi: {{ Str::limit($q->user->allergies, 20) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="mono-num small">{{ $q->user->noId }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $q->poli->name }}</div>
                                    <small class="text-muted">{{ $q->poli->room }}</small>
                                </td>
                                <td class="small text-secondary" style="max-width: 200px;">
                                    {{ $q->complaint }}
                                </td>
                                <td>
                                    <span class="badge {{ $q->status_badge_class }}">
                                        {{ $q->status_label }}
                                    </span>
                                </td>
                                <td class="small text-muted" style="max-width: 180px;">
                                    {{ $q->doctor_notes ?? '-' }}
                                </td>
                                <td class="text-center">
                                    {{-- Clinical Action Center: Tertata Rapi dengan Standar SIMRS --}}
                                    <div class="d-flex justify-content-center align-items-center">
                                        @if ($q->status === 'waiting')
                                            <div class="btn-group">
                                                <form action="{{ route('admin.queue.status', $q->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="in_progress">
                                                    <button type="submit" class="btn btn-primary btn-sm fw-semibold rounded-start-2 px-3 shadow-xs" title="Panggil ke Ruang Periksa">
                                                        <i class="bi bi-megaphone me-1"></i> Panggil
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split rounded-end-2" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Tindakan Lain">
                                                    <span class="visually-hidden">Aksi</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li>
                                                        <form action="{{ route('admin.queue.status', $q->id) }}" method="POST" onsubmit="return confirm('Batalkan antrian {{ $q->queue_code }}?')">
                                                            @csrf
                                                            <input type="hidden" name="status" value="cancelled">
                                                            <button type="submit" class="dropdown-item text-danger small">
                                                                <i class="bi bi-x-circle me-2"></i> Batalkan Antrian
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        @elseif ($q->status === 'skipped')
                                            <div class="btn-group">
                                                <form action="{{ route('admin.queue.status', $q->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="in_progress">
                                                    <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold rounded-start-2 px-3 shadow-xs" title="Panggil Ulang Pasien">
                                                        <i class="bi bi-arrow-repeat me-1"></i> Panggil Ulang
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-outline-warning dropdown-toggle dropdown-toggle-split rounded-end-2" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="visually-hidden">Aksi</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li>
                                                        <form action="{{ route('admin.queue.status', $q->id) }}" method="POST" onsubmit="return confirm('Batalkan antrian {{ $q->queue_code }}?')">
                                                            @csrf
                                                            <input type="hidden" name="status" value="cancelled">
                                                            <button type="submit" class="dropdown-item text-danger small">
                                                                <i class="bi bi-x-circle me-2"></i> Batalkan Antrian
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        @elseif ($q->status === 'in_progress')
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-success btn-sm fw-bold rounded-start-2 px-3 shadow-xs" data-bs-toggle="modal" data-bs-target="#completeModal{{ $q->id }}" title="Buka Rekam Medis SOAP & Peresepan">
                                                    <i class="bi bi-clipboard2-pulse me-1"></i> Periksa (SOAP)
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success dropdown-toggle dropdown-toggle-split rounded-end-2" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="visually-hidden">Aksi Lain</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li>
                                                        <form action="{{ route('admin.queue.status', $q->id) }}" method="POST" onsubmit="return confirm('Pasien {{ $q->queue_code }} belum hadir di ruang periksa? Lewati untuk dipanggil nanti?')">
                                                            @csrf
                                                            <input type="hidden" name="status" value="skipped">
                                                            <button type="submit" class="dropdown-item text-warning fw-semibold small">
                                                                <i class="bi bi-skip-forward me-2"></i> Lewati (Pasien Belum Hadir)
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider my-1"></li>
                                                    <li>
                                                        <form action="{{ route('admin.queue.status', $q->id) }}" method="POST" onsubmit="return confirm('Batalkan antrian {{ $q->queue_code }}?')">
                                                            @csrf
                                                            <input type="hidden" name="status" value="cancelled">
                                                            <button type="submit" class="dropdown-item text-danger small">
                                                                <i class="bi bi-x-circle me-2"></i> Batalkan Antrian
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        @elseif ($q->status === 'completed')
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2 fw-semibold">
                                                    <i class="bi bi-check2-all me-1"></i> Selesai
                                                </span>
                                                @if ($q->medicalRecord)
                                                    <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2 rounded-2 shadow-none" data-bs-toggle="modal" data-bs-target="#viewSoapModal{{ $q->id }}" title="Lihat Rekam Medis Elektronik (RME)">
                                                        <i class="bi bi-journal-medical me-1"></i> RME
                                                    </button>
                                                @endif
                                            </div>
                                        @elseif ($q->status === 'cancelled')
                                            <span class="badge bg-light text-muted border py-1 px-2">
                                                <i class="bi bi-slash-circle me-1"></i> Dibatalkan
                                            </span>
                                        @endif
                                    </div>
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

        {{-- Modals Rekam Medis SOAP & Lembar RME (Permenkes No. 24/2022) --}}
        @foreach ($todayQueues as $q)
            @if ($q->status === 'in_progress')
                {{-- Modal Catat Rekam Medis SOAP & Resep Farmasi --}}
                <div class="modal fade" id="completeModal{{ $q->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $q->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl">
                        <div class="modal-content border-0 shadow-lg">
                            <form action="{{ route('admin.queue.status', $q->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="completed">
                                
                                {{-- Modal Clinical Header --}}
                                <div class="modal-header bg-light border-bottom">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-primary text-white mono-num">{{ $q->queue_code }}</span>
                                            <span class="badge bg-light text-dark border mono-num">No. RM: {{ $q->user->formatted_medical_record_number }}</span>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $q->poli->name }}</span>
                                        </div>
                                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalLabel{{ $q->id }}">
                                            Lembar Pelayanan Medis Rawat Jalan (SOAP)
                                        </h5>
                                        <small class="text-secondary">
                                            Pasien: <strong>{{ $q->user->name }}</strong> ({{ $q->user->noId }}) &bull; Gol. Darah: <strong>{{ $q->user->blood_type ?? '-' }}</strong> &bull; DPJP: <strong>{{ $q->poli->doctor_name }}</strong>
                                        </small>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body text-start p-4">
                                    {{-- Clinical Allergy Warning Alert (Patient Safety) --}}
                                    @if ($q->user->allergies && $q->user->allergies !== 'Tidak ada riwayat alergi obat')
                                        <div class="alert alert-danger d-flex align-items-center gap-3 py-2 px-3 mb-3 border-danger shadow-xs">
                                            <i class="bi bi-exclamation-octagon-fill text-danger fs-4 flex-shrink-0"></i>
                                            <div>
                                                <strong class="text-danger">PERINGATAN KESELAMATAN PASIEN (PATIENT SAFETY):</strong>
                                                <div class="fw-bold text-dark">Pasien memiliki riwayat alergi: <span class="text-danger">{{ $q->user->allergies }}</span>.</div>
                                                <small class="text-secondary">Pastikan tidak meresepkan obat golongan terkait guna mencegah reaksi anafilaksis.</small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-success d-flex align-items-center justify-content-between py-2 px-3 mb-3 border-success-subtle bg-success-subtle text-success small">
                                            <span><i class="bi bi-shield-check me-1"></i> <strong>Riwayat Alergi:</strong> {{ $q->user->allergies ?? 'Tidak ada riwayat alergi obat yang dilaporkan' }}</span>
                                            <span class="badge bg-white text-success border">Alergi Negatif</span>
                                        </div>
                                    @endif

                                    <div class="row g-4">
                                        {{-- Left Column: S (Subjective) & O (Objective) --}}
                                        <div class="col-lg-6">
                                            {{-- S: Subjektif (Anamnesis) --}}
                                            <div class="border rounded-3 p-3 bg-white shadow-xs mb-3">
                                                <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                                                    <span class="badge bg-primary text-white fw-bold">S</span>
                                                    <h6 class="fw-bold text-dark mb-0">Subjektif (Anamnesis & Keluhan)</h6>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-bold text-muted mb-1">Keluhan Utama (Chief Complaint):</label>
                                                    <p class="small text-dark p-2 bg-light border rounded mb-2 fw-medium">{{ $q->complaint }}</p>
                                                </div>
                                                <div class="mb-2">
                                                    <label for="symptoms_{{ $q->id }}" class="form-label small fw-bold text-secondary mb-1">
                                                        Riwayat Penyakit Sekarang (RPS) / Anamnesis Lanjutan:
                                                    </label>
                                                    <input type="text" class="form-control form-control-sm" id="symptoms_{{ $q->id }}" name="symptoms" 
                                                           placeholder="Contoh: Gejala dirasakan 3 hari, demam naik turun, sudah minum paracetamol">
                                                </div>
                                                <div>
                                                    <label class="form-label small fw-bold text-secondary mb-1">Konfirmasi Riwayat Alergi:</label>
                                                    <input type="text" class="form-control form-control-sm" name="allergies_checked" 
                                                           value="{{ $q->user->allergies ?? 'Tidak ada riwayat alergi obat' }}">
                                                </div>
                                            </div>

                                            {{-- O: Objektif (Tanda Vital TTV & Fisik) --}}
                                            <div class="border rounded-3 p-3 bg-white shadow-xs">
                                                <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                                                    <span class="badge bg-info text-white fw-bold">O</span>
                                                    <h6 class="fw-bold text-dark mb-0">Objektif (Tanda-Tanda Vital / TTV)</h6>
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-6 col-md-4">
                                                        <label class="form-label small text-muted mb-1 fw-semibold">Tekanan Darah</label>
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" name="blood_pressure" class="form-control" placeholder="120/80">
                                                            <span class="input-group-text bg-light text-muted">mmHg</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-4">
                                                        <label class="form-label small text-muted mb-1 fw-semibold">Nadi (HR)</label>
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" name="heart_rate" class="form-control" placeholder="80">
                                                            <span class="input-group-text bg-light text-muted">bpm</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-4">
                                                        <label class="form-label small text-muted mb-1 fw-semibold">Suhu Tubuh</label>
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" name="temperature" class="form-control" placeholder="36.5">
                                                            <span class="input-group-text bg-light text-muted">&deg;C</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-4">
                                                        <label class="form-label small text-muted mb-1 fw-semibold">Nafas (RR)</label>
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" name="respiratory_rate" class="form-control" placeholder="18">
                                                            <span class="input-group-text bg-light text-muted">x/m</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-4">
                                                        <label class="form-label small text-muted mb-1 fw-semibold">Berat Badan</label>
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" name="weight" class="form-control" placeholder="60">
                                                            <span class="input-group-text bg-light text-muted">kg</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-4">
                                                        <label class="form-label small text-muted mb-1 fw-semibold">Tinggi Badan</label>
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" name="height" class="form-control" placeholder="168">
                                                            <span class="input-group-text bg-light text-muted">cm</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Right Column: A (Assessment) & P (Plan) --}}
                                        <div class="col-lg-6">
                                            {{-- A: Asesmen (Diagnosis Standar ICD-10) --}}
                                            <div class="border rounded-3 p-3 bg-white shadow-xs mb-3">
                                                <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                                                    <span class="badge bg-warning text-dark fw-bold">A</span>
                                                    <h6 class="fw-bold text-dark mb-0">Asesmen (Diagnosis Kerja ICD-10)</h6>
                                                </div>
                                                <div class="row g-2 mb-2">
                                                    <div class="col-md-8">
                                                        <label for="diagnosis_{{ $q->id }}" class="form-label small fw-bold">
                                                            Diagnosis Klinis <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control form-control-sm" id="diagnosis_{{ $q->id }}" name="diagnosis" 
                                                               placeholder="Contoh: Faringitis Akut / ISPA" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="icd10_{{ $q->id }}" class="form-label small fw-bold">Kode ICD-10</label>
                                                        <input type="text" class="form-control form-control-sm mono-num" id="icd10_{{ $q->id }}" name="icd10_code" placeholder="J02.9">
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <span class="kc-symptom-tag" onclick="document.getElementById('diagnosis_{{ $q->id }}').value='Faringitis Akut'; document.getElementById('icd10_{{ $q->id }}').value='J02.9';">J02.9 Faringitis</span>
                                                    <span class="kc-symptom-tag" onclick="document.getElementById('diagnosis_{{ $q->id }}').value='ISPA Akut'; document.getElementById('icd10_{{ $q->id }}').value='J06.9';">J06.9 ISPA</span>
                                                    <span class="kc-symptom-tag" onclick="document.getElementById('diagnosis_{{ $q->id }}').value='Cephalea Tension'; document.getElementById('icd10_{{ $q->id }}').value='G44.2';">G44.2 Sakit Kepala</span>
                                                    <span class="kc-symptom-tag" onclick="document.getElementById('diagnosis_{{ $q->id }}').value='Gastritis / Dispepsia'; document.getElementById('icd10_{{ $q->id }}').value='K29.7';">K29.7 Dispepsia</span>
                                                    <span class="kc-symptom-tag" onclick="document.getElementById('diagnosis_{{ $q->id }}').value='Pulpitis Reversibel Gigi'; document.getElementById('icd10_{{ $q->id }}').value='K04.0';">K04.0 Gigi</span>
                                                </div>
                                            </div>

                                            {{-- P: Plan (Resep Obat & Terapi Edukasi) --}}
                                            <div class="border rounded-3 p-3 bg-white shadow-xs">
                                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge bg-success text-white fw-bold">P</span>
                                                        <h6 class="fw-bold text-dark mb-0">Plan (Resep Farmasi Elektronik & Edukasi)</h6>
                                                    </div>
                                                    <span class="badge bg-light text-muted border" style="font-size: 0.675rem;">Stok Otomatis</span>
                                                </div>

                                                    <div class="alert alert-light border py-1 px-2 small text-muted mb-3 d-flex align-items-center gap-2">
                                                        <i class="bi bi-info-circle text-primary flex-shrink-0"></i>
                                                        <span><strong>Resep Bersifat Opsional:</strong> Kosongkan jika pasien hanya memerlukan konsultasi, edukasi, atau tindakan non-farmakoterapi.</span>
                                                    </div>

                                                    {{-- Prescription Item Row 1 --}}
                                                    <div class="row g-2 align-items-center mb-2">
                                                        <div class="col-md-5">
                                                            <select name="medicines[0][id]" class="form-select form-select-sm" onchange="autoFillPrescription(this, 'qty_{{ $q->id }}_0', 'dos_{{ $q->id }}_0')">
                                                                <option value="">-- Tanpa Obat / Pilih Obat 1 --</option>
                                                                @foreach ($medicines as $med)
                                                                    <option value="{{ $med->id }}" {{ $med->isOutOfStock() ? 'disabled' : '' }}>
                                                                        {{ $med->name }} (Sisa: {{ $med->stock }} {{ $med->unit }}) {{ $med->isOutOfStock() ? '[HABIS]' : '' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="number" id="qty_{{ $q->id }}_0" name="medicines[0][quantity]" class="form-control form-control-sm" min="1" placeholder="Jumlah">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" id="dos_{{ $q->id }}_0" name="medicines[0][dosage]" class="form-control form-control-sm" placeholder="Aturan pakai / dosis">
                                                        </div>
                                                    </div>

                                                    {{-- Prescription Item Row 2 --}}
                                                    <div class="row g-2 align-items-center mb-2">
                                                        <div class="col-md-5">
                                                            <select name="medicines[1][id]" class="form-select form-select-sm" onchange="autoFillPrescription(this, 'qty_{{ $q->id }}_1', 'dos_{{ $q->id }}_1')">
                                                                <option value="">-- Tanpa Obat / Pilih Obat 2 --</option>
                                                                @foreach ($medicines as $med)
                                                                    <option value="{{ $med->id }}" {{ $med->isOutOfStock() ? 'disabled' : '' }}>
                                                                        {{ $med->name }} (Sisa: {{ $med->stock }} {{ $med->unit }}) {{ $med->isOutOfStock() ? '[HABIS]' : '' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="number" id="qty_{{ $q->id }}_1" name="medicines[1][quantity]" class="form-control form-control-sm" min="1" placeholder="Jumlah">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" id="dos_{{ $q->id }}_1" name="medicines[1][dosage]" class="form-control form-control-sm" placeholder="Aturan pakai / dosis">
                                                        </div>
                                                    </div>

                                                    {{-- Prescription Item Row 3 --}}
                                                    <div class="row g-2 align-items-center mb-3">
                                                        <div class="col-md-5">
                                                            <select name="medicines[2][id]" class="form-select form-select-sm" onchange="autoFillPrescription(this, 'qty_{{ $q->id }}_2', 'dos_{{ $q->id }}_2')">
                                                                <option value="">-- Tanpa Obat / Pilih Obat 3 --</option>
                                                                @foreach ($medicines as $med)
                                                                    <option value="{{ $med->id }}" {{ $med->isOutOfStock() ? 'disabled' : '' }}>
                                                                        {{ $med->name }} (Sisa: {{ $med->stock }} {{ $med->unit }}) {{ $med->isOutOfStock() ? '[HABIS]' : '' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="number" id="qty_{{ $q->id }}_2" name="medicines[2][quantity]" class="form-control form-control-sm" min="1" placeholder="Jumlah">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" id="dos_{{ $q->id }}_2" name="medicines[2][dosage]" class="form-control form-control-sm" placeholder="Aturan pakai / dosis">
                                                        </div>
                                                    </div>

                                                <div>
                                                    <label for="doctor_notes_{{ $q->id }}" class="form-label small fw-bold text-secondary mb-1">
                                                        Edukasi & Rencana Tindak Lanjut Pasien:
                                                    </label>
                                                    <textarea class="form-control form-control-sm" id="doctor_notes_{{ $q->id }}" name="doctor_notes" rows="2" 
                                                              placeholder="Instruksi istirahat, diet nutrisi khusus, anjuran kontrol ulang jika keluhan menetap..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer bg-light border-top">
                                    <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-success btn-sm fw-bold rounded-3 shadow-xs">
                                        <i class="bi bi-check2-circle me-1"></i> Simpan Rekam Medis (SOAP) & Selesaikan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @elseif ($q->status === 'completed' && $q->medicalRecord)
                {{-- Modal Lihat Rekam Medis Elektronik (RME) Selesai --}}
                <div class="modal fade" id="viewSoapModal{{ $q->id }}" tabindex="-1" aria-labelledby="viewSoapLabel{{ $q->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-light border-bottom">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-success text-white"><i class="bi bi-check-circle me-1"></i> Selesai Diperiksa</span>
                                        <span class="badge bg-light text-dark border mono-num">{{ $q->medicalRecord->record_code }}</span>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $q->poli->name }}</span>
                                    </div>
                                    <h5 class="modal-title fw-bold text-dark mb-0" id="viewSoapLabel{{ $q->id }}">
                                        Rekam Medis Elektronik (RME Rawat Jalan)
                                    </h5>
                                    <small class="text-muted">
                                        Pasien: <strong>{{ $q->user->name }}</strong> (No. RM: <strong>{{ $q->user->formatted_medical_record_number }}</strong>) &bull; DPJP: <strong>{{ $q->medicalRecord->doctor_name }}</strong>
                                    </small>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                {{-- Pasien Info Ringkas --}}
                                <div class="row g-2 mb-3 p-3 bg-light rounded-3 small">
                                    <div class="col-6 col-md-3">
                                        <span class="text-muted d-block">Nomor Identitas:</span>
                                        <strong class="mono-num text-dark">{{ $q->user->noId }}</strong>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <span class="text-muted d-block">Gol. Darah / Gender:</span>
                                        <strong class="text-dark">{{ $q->user->blood_type ?? '-' }} / {{ $q->user->gender }}</strong>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <span class="text-muted d-block">Tanggal Pelayanan:</span>
                                        <strong class="text-dark">{{ $q->visit_date->format('d/m/Y') }}</strong>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <span class="text-muted d-block">Status Alergi Obat:</span>
                                        <strong class="{{ $q->user->allergies && $q->user->allergies !== 'Tidak ada riwayat alergi obat' ? 'text-danger' : 'text-success' }}">
                                            {{ $q->user->allergies ?? 'Alergi (-)' }}
                                        </strong>
                                    </div>
                                </div>

                                {{-- Lembar SOAP --}}
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="border rounded-3 p-3 bg-white h-100">
                                            <div class="fw-bold text-primary mb-2 border-bottom pb-1 small">
                                                <span class="badge bg-primary text-white me-1">S</span> SUBJEKTIF (ANAMNESIS)
                                            </div>
                                            <div class="small mb-2">
                                                <span class="text-muted d-block fw-semibold">Keluhan Utama:</span>
                                                <div class="text-dark">{{ $q->complaint }}</div>
                                            </div>
                                            @if ($q->medicalRecord->symptoms)
                                                <div class="small">
                                                    <span class="text-muted d-block fw-semibold">Riwayat Penyakit Sekarang:</span>
                                                    <div class="text-dark">{{ $q->medicalRecord->symptoms }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="border rounded-3 p-3 bg-white h-100">
                                            <div class="fw-bold text-info mb-2 border-bottom pb-1 small">
                                                <span class="badge bg-info text-white me-1">O</span> OBJEKTIF (TANDA VITAL / TTV)
                                            </div>
                                            <div class="row g-2 small">
                                                <div class="col-6">
                                                    <span class="text-muted">Tekanan Darah:</span>
                                                    <div class="fw-bold text-dark">{{ $q->medicalRecord->blood_pressure ? $q->medicalRecord->blood_pressure . ' mmHg' : '-' }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-muted">Nadi (HR):</span>
                                                    <div class="fw-bold text-dark">{{ $q->medicalRecord->heart_rate ? $q->medicalRecord->heart_rate . ' bpm' : '-' }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-muted">Suhu Tubuh:</span>
                                                    <div class="fw-bold text-dark">{{ $q->medicalRecord->temperature ? $q->medicalRecord->temperature . ' °C' : '-' }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-muted">Pernapasan (RR):</span>
                                                    <div class="fw-bold text-dark">{{ $q->medicalRecord->respiratory_rate ? $q->medicalRecord->respiratory_rate . ' x/m' : '-' }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-muted">Berat / Tinggi:</span>
                                                    <div class="fw-bold text-dark">{{ $q->medicalRecord->weight ?? '-' }} kg / {{ $q->medicalRecord->height ?? '-' }} cm</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="border rounded-3 p-3 bg-white">
                                            <div class="fw-bold text-warning mb-2 border-bottom pb-1 small">
                                                <span class="badge bg-warning text-dark me-1">A</span> ASESMEN (DIAGNOSIS KERJA)
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <h6 class="fw-bold text-dark mb-0">{{ $q->medicalRecord->diagnosis }}</h6>
                                                @if ($q->medicalRecord->icd10_code)
                                                    <span class="badge bg-warning-subtle text-dark border mono-num">ICD-10: {{ $q->medicalRecord->icd10_code }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="border rounded-3 p-3 bg-white">
                                            <div class="fw-bold text-success mb-2 border-bottom pb-1 small">
                                                <span class="badge bg-success text-white me-1">P</span> PLAN (TERAPI OBAT & EDUKASI PASIEN)
                                            </div>
                                            @if ($q->medicalRecord->prescriptions->isNotEmpty())
                                                <div class="table-responsive mb-2">
                                                    <table class="table table-sm table-bordered align-middle mb-0 small">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Obat Resep</th>
                                                                <th class="text-center" style="width: 80px;">Jumlah</th>
                                                                <th>Aturan Pakai / Signa</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($q->medicalRecord->prescriptions as $item)
                                                                <tr>
                                                                    <td class="fw-semibold">{{ $item->medicine->name ?? 'Obat Farmasi' }}</td>
                                                                    <td class="text-center mono-num">{{ $item->quantity }} {{ $item->medicine->unit ?? '' }}</td>
                                                                    <td>{{ $item->dosage }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="small text-muted mb-2">Tidak ada peresepan obat farmasi pada kunjungan ini.</p>
                                            @endif

                                            @if ($q->medicalRecord->treatment_notes)
                                                <div class="p-2 bg-light rounded small mt-2">
                                                    <span class="fw-bold text-dark d-block">Catatan & Rencana Tindak Lanjut:</span>
                                                    <span class="text-secondary">{{ $q->medicalRecord->treatment_notes }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 p-2 bg-light border rounded text-center small text-muted">
                                    <i class="bi bi-shield-check text-success me-1"></i>
                                    Dokumen Rekam Medis Elektronik (RME) telah tersimpan secara permanen & sah sesuai <strong>Permenkes RI No. 24 Tahun 2022</strong>.
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top">
                                <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Master Data Poliklinik & Pengaturan Dokter Jaga (DPJP) --}}
        <div class="bg-white border rounded-4 shadow-sm p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Status & Jadwal Dokter 4 Poliklinik</h5>
                    <p class="text-secondary small mb-0">Kelola dokter penanggung jawab pelayanan (DPJP), jam buka/tutup, dan kuota pasien harian.</p>
                </div>
            </div>
            <div class="row g-3">
                @foreach ($polies as $p)
                    <div class="col-md-6 col-lg-3">
                        <div class="border rounded-3 p-3 bg-light h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ asset($p->icon_path) }}" alt="{{ $p->name }}" height="26" onerror="this.src='{{ asset('img/klickCare-logo.png') }}'">
                                        <h6 class="fw-bold mb-0 text-dark">{{ $p->name }}</h6>
                                    </div>
                                    <span class="badge {{ $p->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border" style="font-size: 0.675rem;">
                                        {{ $p->is_active ? 'Buka' : 'Libur' }}
                                    </span>
                                </div>
                                <div class="small text-dark fw-semibold mb-1">
                                    <i class="bi bi-person-fill text-primary me-1"></i> {{ $p->doctor_name }}
                                </div>
                                <div class="small text-muted mb-1">
                                    <i class="bi bi-geo-alt me-1"></i> {{ $p->room }}
                                </div>
                                <div class="small text-muted mb-2">
                                    <i class="bi bi-clock me-1"></i> {{ $p->open_time }} - {{ $p->close_time }} WIB
                                </div>
                            </div>
                            <div class="border-top pt-2">
                                <div class="d-flex justify-content-between align-items-center small mb-2">
                                    <span class="text-muted">Sisa Kuota:</span>
                                    <span class="mono-num fw-bold text-primary">{{ $p->remainingQuotaToday() }} / {{ $p->daily_quota }}</span>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm w-100 rounded-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#editPoliModal{{ $p->id }}" title="Atur Pergantian Dokter & Jadwal Layanan">
                                    <i class="bi bi-pencil-square me-1"></i> Atur Dokter & Jadwal
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modals Pengaturan Tenaga Medis Dokter & Poliklinik (Admin Role 1) --}}
    @foreach ($polies as $p)
        <div class="modal fade" id="editPoliModal{{ $p->id }}" tabindex="-1" aria-labelledby="editPoliLabel{{ $p->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <form action="{{ route('admin.poli.update', $p->id) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-light border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ asset($p->icon_path) }}" alt="{{ $p->name }}" height="24" onerror="this.src='{{ asset('img/klickCare-logo.png') }}'">
                                <h5 class="modal-title fw-bold text-dark mb-0" id="editPoliLabel{{ $p->id }}">
                                    Atur Dokter & Operasional {{ $p->name }}
                                </h5>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label for="doc_{{ $p->id }}" class="form-label small fw-bold">Nama Dokter Jaga / DPJP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="doc_{{ $p->id }}" name="doctor_name" value="{{ $p->doctor_name }}" required placeholder="Contoh: dr. H. Hendra Setiawan, Sp.PD atau dr. Pengganti">
                                <small class="text-muted">Tuliskan nama lengkap beserta gelar dokter yang bertugas hari ini.</small>
                            </div>

                            <div class="mb-3">
                                <label for="room_{{ $p->id }}" class="form-label small fw-bold">Ruangan Pelayanan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="room_{{ $p->id }}" name="room" value="{{ $p->room }}" required>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label for="open_{{ $p->id }}" class="form-label small fw-bold">Jam Buka Layanan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="open_{{ $p->id }}" name="open_time" value="{{ $p->open_time }}" required placeholder="08:00">
                                </div>
                                <div class="col-6">
                                    <label for="close_{{ $p->id }}" class="form-label small fw-bold">Jam Tutup Layanan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="close_{{ $p->id }}" name="close_time" value="{{ $p->close_time }}" required placeholder="15:30">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="quota_{{ $p->id }}" class="form-label small fw-bold">Batas Kuota Antrian Harian <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control" id="quota_{{ $p->id }}" name="daily_quota" value="{{ $p->daily_quota }}" min="1" max="500" required>
                                    <span class="input-group-text bg-light text-muted">Pasien / Hari</span>
                                </div>
                                <small class="text-muted">Sesuaikan kapasitas kuota jika ada penambahan atau pembatasan dokter jaga.</small>
                            </div>

                            <div class="mb-3">
                                <label for="desc_{{ $p->id }}" class="form-label small fw-bold">Deskripsi Layanan Klinis</label>
                                <textarea class="form-control form-control-sm" id="desc_{{ $p->id }}" name="description" rows="2">{{ $p->description }}</textarea>
                            </div>

                            <div class="form-check form-switch p-2 bg-light rounded-3 border">
                                <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="active_{{ $p->id }}" name="is_active" value="1" {{ $p->is_active ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold text-dark" for="active_{{ $p->id }}">
                                    Layanan Poliklinik Buka & Menerima Antrian Pasien
                                </label>
                                <div class="small text-muted ps-4">Nonaktifkan jika poli sedang libur atau dokter berhalangan tanpa pengganti.</div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top">
                            <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm fw-bold rounded-3 shadow-xs">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan Dokter & Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function autoFillPrescription(selectEl, qtyInputId, dosInputId) {
            const qtyInput = document.getElementById(qtyInputId);
            const dosInput = document.getElementById(dosInputId);
            if (!qtyInput || !dosInput) return;

            if (selectEl.value) {
                if (!qtyInput.value) qtyInput.value = 1;
                if (!dosInput.value) dosInput.value = '3x1 tablet sesudah makan';
            } else {
                qtyInput.value = '';
                dosInput.value = '';
            }
        }
    </script>
</body>
</html>
