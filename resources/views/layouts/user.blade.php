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
    @include('components.toast')

    <div class="container py-4">

        {{-- Patient Header Banner with Rich Visual Styling --}}
        <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm position-relative overflow-hidden">
            <div class="position-absolute end-0 top-0 bottom-0 opacity-10 d-none d-md-block pe-4 pt-2">
                <i class="bi bi-hospital fs-1" style="font-size: 8rem !important; color: #1e3a8a;"></i>
            </div>

            <div class="row align-items-center justify-content-between g-3 position-relative">
                <div class="col-md-7">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 mb-2 fw-semibold">
                        <i class="bi bi-person-circle me-1"></i> Portal Pasien Terpadu
                    </span>
                    <h2 class="fw-bold mb-1">Selamat Datang, {{ $user->name }}</h2>
                    <p class="text-secondary small mb-0">
                        <span class="me-3"><i class="bi bi-card-heading text-primary me-1"></i> NIM/NIP: <strong class="mono-num text-dark">{{ $user->noId }}</strong></span>
                        <span class="me-3"><i class="bi bi-envelope text-primary me-1"></i> {{ $user->email }}</span>
                        <span><i class="bi bi-award-fill text-warning me-1"></i> {{ $user->role_label }}</span>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="badge bg-light text-dark border p-2 text-start shadow-xs">
                        <small class="text-muted d-block">Tanggal Pelayanan:</small>
                        <span class="fw-bold"><i class="bi bi-calendar-event text-primary me-1"></i> {{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            {{-- Left Column: Active Queue Card (Ticket Design) --}}
            <div class="col-lg-5">
                <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                        <h5 class="fw-bold mb-0">Tiket Antrian Digital Anda</h5>
                        <i class="bi bi-qr-code-scan text-primary fs-4"></i>
                    </div>

                    @if ($activeQueue)
                        <div class="kc-ticket-card text-center mb-3">
                            <span class="badge {{ $activeQueue->status_badge_class }} text-uppercase px-3 py-1 mb-3">
                                @if ($activeQueue->status === 'in_progress')
                                    <span class="kc-pulse-dot d-inline-block me-1"></span>
                                @endif
                                {{ $activeQueue->status_label }}
                            </span>

                            <div class="mono-num fw-bold text-primary mb-2" style="font-size: 3.5rem; line-height: 1; letter-spacing: -0.03em;">
                                {{ $activeQueue->queue_code }}
                            </div>

                            <div class="text-dark fw-bold fs-5">{{ $activeQueue->poli->name }}</div>
                            <div class="text-secondary small mb-3"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $activeQueue->poli->room }}</div>

                            <div class="p-3 bg-light rounded-3 text-start border small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Dokter Jaga:</span>
                                    <span class="fw-bold text-dark">{{ $activeQueue->poli->doctor_name }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Jam Layanan:</span>
                                    <span class="fw-semibold text-dark">{{ $activeQueue->poli->open_time }} - {{ $activeQueue->poli->close_time }} WIB</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Keluhan:</span>
                                    <span class="text-end text-dark fw-medium" style="max-width: 60%;">{{ $activeQueue->complaint }}</span>
                                </div>
                            </div>

                            {{-- Live Queue Position & Status Notice --}}
                            @if ($activeQueue->status === 'waiting')
                                <div class="mt-2 p-2 bg-primary-subtle border border-primary-subtle rounded-3 text-center">
                                    <span class="small text-primary fw-semibold">
                                        <i class="bi bi-people-fill me-1"></i>
                                        @if (($waitingAheadCount ?? 0) === 0)
                                            <strong>Anda antrian berikutnya!</strong> Harap bersiap di ruang tunggu.
                                        @else
                                            Ada <strong>{{ $waitingAheadCount }}</strong> pasien di depan Anda (est. ~{{ $waitingAheadCount * 10 }} mnt).
                                        @endif
                                    </span>
                                </div>
                            @elseif ($activeQueue->status === 'in_progress')
                                <div class="mt-2 p-2 bg-success-subtle border border-success-subtle rounded-3 text-center">
                                    <span class="small text-success fw-bold">
                                        <span class="kc-pulse-dot d-inline-block me-1"></span> Giliran Anda Sekarang! Silakan masuk ke ruang dokter.
                                    </span>
                                </div>
                            @elseif ($activeQueue->status === 'skipped')
                                <div class="mt-2 p-2 bg-warning-subtle border border-warning rounded-3 text-center">
                                    <span class="small text-warning-emphasis fw-bold">
                                        <i class="bi bi-clock-history me-1"></i> Panggilan Anda Sempat Dilewati
                                        <div class="small fw-normal text-secondary mt-1">Harap bersiap di depan ruang dokter. Dokter akan memanggil ulang setelah antrian saat ini selesai.</div>
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Action Buttons: Pantau Layar Poli & Batalkan Antrian --}}
                        <div class="row g-2">
                            <div class="{{ in_array($activeQueue->status, ['waiting', 'skipped']) ? 'col-8' : 'col-12' }}">
                                <a href="{{ route('poli.detail', $activeQueue->poli->slug) }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm w-100 fw-semibold d-flex align-items-center justify-content-center gap-1">
                                    <i class="bi bi-display"></i>
                                    <span>Pantau Layar Poli</span>
                                    <i class="bi bi-box-arrow-up-right small ms-1" style="font-size: 0.725rem;"></i>
                                </a>
                            </div>
                            @if (in_array($activeQueue->status, ['waiting', 'skipped']))
                                <div class="col-4">
                                    <form action="{{ route('queue.cancel', $activeQueue->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan antrian ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 fw-semibold d-flex align-items-center justify-content-center gap-1">
                                            <i class="bi bi-x-circle"></i>
                                            <span>Batal</span>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <div class="fs-1 text-secondary opacity-50 mb-3"><i class="bi bi-ticket-perforated"></i></div>
                            <h6 class="fw-bold text-dark">Belum Ada Antrian Aktif Hari Ini</h6>
                            <p class="small text-secondary mb-0">Silakan pilih poliklinik tujuan pada formulir di samping untuk mengambil nomor antrian periksa.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Column: Form Ambil Antrian --}}
            <div class="col-lg-7">
                <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
                    <div class="border-bottom pb-3 mb-3">
                        <h5 class="fw-bold mb-1">Pendaftaran Kunjungan & Antrian Baru</h5>
                        <p class="text-secondary small mb-0">Pilih poli tujuan dan tuliskan keluhan medis Anda secara ringkas.</p>
                    </div>

                    <form action="{{ route('queue.store') }}" method="POST" id="queueBookingForm">
                        @csrf

                        {{-- 1. Visual Poli Selector Cards --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small fw-bold text-secondary mb-0">
                                    <i class="bi bi-hospital text-primary me-1"></i> Pilih Poliklinik Tujuan <span class="text-danger">*</span>
                                </label>
                                <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">Pilih salah satu layanan</span>
                            </div>

                            {{-- Hidden Native Select for 100% standard form submission & validation --}}
                            <select class="form-select d-none @error('poli_id') is-invalid @enderror" id="poli_id" name="poli_id" required>
                                <option value="" disabled {{ old('poli_id') ? '' : 'selected' }}>-- Pilih Layanan Poli --</option>
                                @foreach ($polies as $p)
                                    <option value="{{ $p->id }}" {{ old('poli_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Interactive 2x2 Grid Selection --}}
                            <div class="row g-2" id="poliSelectionGrid">
                                @foreach ($polies as $p)
                                    <div class="col-sm-6">
                                        <div class="kc-poli-radio-card {{ old('poli_id') == $p->id ? 'selected' : '' }}" data-poli-id="{{ $p->id }}" role="button">
                                            <div class="d-flex align-items-start justify-content-between mb-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="{{ asset($p->icon_path) }}" alt="{{ $p->name }}" height="26" onerror="this.src='{{ asset('img/klickCare-logo.png') }}'">
                                                    <div>
                                                        <div class="fw-bold text-dark small" style="font-size: 0.875rem;">{{ $p->name }}</div>
                                                        <div class="text-muted" style="font-size: 0.7rem;">{{ $p->room }}</div>
                                                    </div>
                                                </div>
                                                <div class="kc-poli-radio-check"></div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center pt-1 border-top small" style="font-size: 0.725rem;">
                                                <span class="text-truncate text-secondary" style="max-width: 110px;">{{ $p->doctor_name }}</span>
                                                <span class="mono-num fw-bold {{ $p->remainingQuotaToday() > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $p->remainingQuotaToday() }} / {{ $p->daily_quota }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('poli_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 2. Stylized Date Picker & Quick Action Chips --}}
                        <div class="mb-3">
                            <label for="visit_date" class="form-label small fw-bold text-secondary mb-1">
                                <i class="bi bi-calendar-event text-primary me-1"></i> Tanggal Kunjungan <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-primary border-end-0"><i class="bi bi-calendar-check"></i></span>
                                <input type="date" class="form-control border-start-0 @error('visit_date') is-invalid @enderror" id="visit_date" name="visit_date" 
                                       value="{{ old('visit_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <button type="button" class="kc-quick-chip active" id="chipToday">
                                    <i class="bi bi-calendar-check"></i> Hari Ini ({{ now()->translatedFormat('d M') }})
                                </button>
                                <button type="button" class="kc-quick-chip" id="chipTomorrow">
                                    <i class="bi bi-calendar-plus"></i> Besok ({{ now()->addDay()->translatedFormat('d M') }})
                                </button>
                            </div>
                            @error('visit_date')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 3. Enhanced Complaint Field with Quick Symptom Tags --}}
                        <div class="mb-4">
                            <label for="complaint" class="form-label small fw-bold text-secondary mb-1">
                                <i class="bi bi-chat-heart text-primary me-1"></i> Keluhan Utama & Alasan Kunjungan <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted align-items-start pt-2 border-end-0"><i class="bi bi-pencil"></i></span>
                                <textarea class="form-control border-start-0 @error('complaint') is-invalid @enderror" id="complaint" name="complaint" rows="3" 
                                          placeholder="Jelaskan secara singkat gejala kesehatan yang dirasakan..." required>{{ old('complaint') }}</textarea>
                            </div>
                            
                            {{-- Quick Symptom Suggestions --}}
                            <div class="mt-2">
                                <small class="text-muted d-block mb-1" style="font-size: 0.725rem;">Klik untuk menambahkan gejala umum:</small>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="kc-symptom-tag" data-tag="Demam dan pusing">+ Demam & Pusing</span>
                                    <span class="kc-symptom-tag" data-tag="Batuk, flu dan sakit tenggorokan">+ Flu & Batuk</span>
                                    <span class="kc-symptom-tag" data-tag="Nyeri sakit gigi dan gusi">+ Sakit Gigi</span>
                                    <span class="kc-symptom-tag" data-tag="Mata perih, merah dan iritasi">+ Iritasi Mata</span>
                                    <span class="kc-symptom-tag" data-tag="Nyeri perut / lambung (maag)">+ Sakit Perut</span>
                                    <span class="kc-symptom-tag" data-tag="Konsultasi pola makan dan gizi">+ Konsultasi Gizi</span>
                                </div>
                            </div>
                            @error('complaint')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-kc-primary w-100 py-2 fw-bold shadow-sm">
                            <i class="bi bi-send-check me-1"></i> Terbitkan Nomor Antrian Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Rekam Medis Elektronik (RME) & Resep Obat Pasien --}}
        <div class="bg-white border rounded-4 p-4 shadow-sm mb-4">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                <div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1">
                        <i class="bi bi-shield-lock-fill me-1"></i> Rekam Medis Terverifikasi & Terenkripsi
                    </span>
                    <h5 class="fw-bold mb-1">Catatan Rekam Medis & Resep Obat Elektronik</h5>
                    <p class="text-secondary small mb-0">Arsip riwayat diagnosa dokter, tanda vital klinis, dan detail resep obat yang telah diresepkan.</p>
                </div>
            </div>

            @if ($medicalRecords->count() > 0)
                <div class="d-flex flex-column gap-3">
                    @foreach ($medicalRecords as $record)
                        <div class="border rounded-3 p-3 bg-light hover-elevate transition-all">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom pb-2 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary text-white mono-num">{{ $record->record_code }}</span>
                                    <span class="fw-bold text-dark">{{ $record->poli->name }}</span>
                                    <small class="text-muted">&bull; {{ $record->doctor_name }}</small>
                                </div>
                                <div class="small text-muted">
                                    <i class="bi bi-calendar3 me-1 text-primary"></i> {{ $record->created_at->translatedFormat('d F Y, H:i') }} WIB
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-7">
                                    <div class="mb-2">
                                        <small class="text-muted d-block fw-semibold" style="font-size: 0.75rem;">DIAGNOSIS KLINIS:</small>
                                        <div class="fw-bold text-dark fs-6">{{ $record->diagnosis }}</div>
                                    </div>
                                    @if ($record->symptoms)
                                        <div class="mb-2">
                                            <small class="text-muted d-block fw-semibold" style="font-size: 0.75rem;">GEJALA & ANAMNESIS:</small>
                                            <span class="small text-secondary">{{ $record->symptoms }}</span>
                                        </div>
                                    @endif
                                    @if ($record->treatment_notes)
                                        <div class="mb-2">
                                            <small class="text-muted d-block fw-semibold" style="font-size: 0.75rem;">ANJURAN & CATATAN DOKTER:</small>
                                            <span class="small text-secondary">{{ $record->treatment_notes }}</span>
                                        </div>
                                    @endif

                                    {{-- Vital Signs Badges --}}
                                    @if ($record->blood_pressure || $record->temperature || $record->weight)
                                        <div class="d-flex flex-wrap gap-2 mt-2 pt-1">
                                            @if ($record->blood_pressure)
                                                <span class="badge bg-white text-dark border">
                                                    <i class="bi bi-heart-pulse text-danger me-1"></i> Tensi: {{ $record->blood_pressure }} mmHg
                                                </span>
                                            @endif
                                            @if ($record->temperature)
                                                <span class="badge bg-white text-dark border">
                                                    <i class="bi bi-thermometer-half text-warning me-1"></i> Suhu: {{ $record->temperature }} &deg;C
                                                </span>
                                            @endif
                                            @if ($record->weight)
                                                <span class="badge bg-white text-dark border">
                                                    <i class="bi bi-speedometer text-info me-1"></i> BB: {{ $record->weight }} kg
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-5">
                                    <div class="bg-white border rounded-3 p-3 h-100">
                                        <small class="text-primary d-block fw-bold mb-2">
                                            <i class="bi bi-capsule me-1"></i> Resep Obat Farmasi Poliklinik:
                                        </small>
                                        @if ($record->prescriptions->count() > 0)
                                            <ul class="list-unstyled mb-0 small">
                                                @foreach ($record->prescriptions as $item)
                                                    <li class="d-flex justify-content-between align-items-start py-1 border-bottom last-border-0">
                                                        <div>
                                                            <span class="fw-bold text-dark">{{ $item->medicine->name }}</span>
                                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                                <i class="bi bi-clock me-1"></i>{{ $item->dosage }}
                                                            </div>
                                                        </div>
                                                        <span class="badge bg-light text-dark border mono-num ms-2">
                                                            {{ $item->quantity }} {{ $item->medicine->unit }}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="small text-muted mb-0 fst-italic">Tidak ada resep obat (tindakan non-farmakologi atau anjuran observasi mandiri).</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($medicalRecords->hasPages())
                    <div class="d-flex justify-content-end mt-3">
                        {{ $medicalRecords->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-4 text-muted small">
                    <i class="bi bi-file-earmark-medical fs-2 text-secondary d-block mb-2"></i>
                    Belum ada rekam medis terbit. Rekam medis akan otomatis tampil setelah dokter menyelesaikan pemeriksaan kunjungan Anda.
                </div>
            @endif
        </div>

        {{-- Riwayat Kunjungan Pasien --}}
        <div class="bg-white border rounded-4 p-4 shadow-sm">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Riwayat Tiket Antrian Kunjungan</h5>
                    <p class="text-secondary small mb-0">Catatan nomor tiket antrian kunjungan ke Poliklinik Polinema.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary">Tanggal Kunjungan</th>
                            <th class="small text-secondary">Nomor Tiket</th>
                            <th class="small text-secondary">Poliklinik & Dokter</th>
                            <th class="small text-secondary">Keluhan Awal</th>
                            <th class="small text-secondary">Status</th>
                            <th class="small text-secondary">Catatan Medis / Resep</th>
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
                                <td class="small text-dark fw-medium">
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Visual Poli Radio Grid Selection
            const selectEl = document.getElementById('poli_id');
            const radioCards = document.querySelectorAll('.kc-poli-radio-card');

            radioCards.forEach(card => {
                card.addEventListener('click', () => {
                    const poliId = card.getAttribute('data-poli-id');
                    radioCards.forEach(c => c.classList.remove('selected'));
                    card.classList.add('selected');
                    if (selectEl) {
                        selectEl.value = poliId;
                    }
                });
            });

            // If select already has a value on page load (e.g. from validation error old input)
            if (selectEl && selectEl.value) {
                const preselectedCard = document.querySelector(`.kc-poli-radio-card[data-poli-id="${selectEl.value}"]`);
                if (preselectedCard) {
                    radioCards.forEach(c => c.classList.remove('selected'));
                    preselectedCard.classList.add('selected');
                }
            }

            // 2. Quick Date Chips
            const dateInput = document.getElementById('visit_date');
            const chipToday = document.getElementById('chipToday');
            const chipTomorrow = document.getElementById('chipTomorrow');

            function formatDate(d) {
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            if (chipToday && dateInput) {
                chipToday.addEventListener('click', () => {
                    const today = new Date();
                    dateInput.value = formatDate(today);
                    chipToday.classList.add('active');
                    if (chipTomorrow) chipTomorrow.classList.remove('active');
                });
            }

            if (chipTomorrow && dateInput) {
                chipTomorrow.addEventListener('click', () => {
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    dateInput.value = formatDate(tomorrow);
                    chipTomorrow.classList.add('active');
                    if (chipToday) chipToday.classList.remove('active');
                });
            }

            // 3. Quick Symptom Tags
            const complaintTextarea = document.getElementById('complaint');
            const symptomTags = document.querySelectorAll('.kc-symptom-tag');

            symptomTags.forEach(tag => {
                tag.addEventListener('click', () => {
                    const symptom = tag.getAttribute('data-tag');
                    if (!complaintTextarea || !symptom) return;
                    const currentVal = complaintTextarea.value.trim();
                    if (!currentVal) {
                        complaintTextarea.value = symptom;
                    } else if (!currentVal.toLowerCase().includes(symptom.toLowerCase())) {
                        complaintTextarea.value = currentVal + ', ' + symptom;
                    }
                    complaintTextarea.focus();
                });
            });
        });
    </script>
</body>
</html>
