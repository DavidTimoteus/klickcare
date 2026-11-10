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
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom pb-3 mb-4">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h5 class="fw-bold mb-0">Layar Antrian Real-Time</h5>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small d-inline-flex align-items-center gap-1">
                                    <span class="kc-pulse-dot"></span> LIVE AUTO-SYNC
                                </span>
                            </div>
                            <span class="text-muted small">{{ now()->translatedFormat('l, d F Y') }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" id="toggleAudioBtn" onclick="toggleAudioCall()" title="Nyalakan/Matikan Panggilan Suara">
                                <i class="bi bi-volume-up-fill me-1" id="audioBtnIcon"></i>
                                <span id="audioBtnText">Suara: Aktif</span>
                            </button>
                            <div class="badge bg-light text-dark border p-2">
                                Kode Awalan: <span class="mono-num fw-bold text-primary">{{ $poli->code_prefix }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Live Active Calling Box --}}
                    <div id="callingBoxContainer">
                        @if ($currentCalling)
                            <div class="alert alert-primary d-flex align-items-center gap-3 p-4 rounded-3 mb-4 shadow-sm" id="callingAlertBox" role="alert">
                                <div class="fs-1 text-primary"><i class="bi bi-megaphone-fill"></i></div>
                                <div>
                                    <span class="badge bg-primary text-white text-uppercase px-2 py-1">Sedang Dipanggil</span>
                                    <h1 class="mono-num fw-bold mb-0 text-primary display-4" id="callingQueueCode">{{ $currentCalling->queue_code }}</h1>
                                    <div class="text-secondary small mt-1" id="callingRoomText">Pasien dipersilakan masuk ke ruang <strong>{{ $poli->room }}</strong>.</div>
                                </div>
                            </div>
                        @else
                            <div class="p-4 bg-light rounded-3 text-center border mb-4" id="callingEmptyBox">
                                <div class="fs-2 text-muted mb-2"><i class="bi bi-hourglass-split"></i></div>
                                <h6 class="fw-bold text-dark mb-1">Menunggu Panggilan Pasien</h6>
                                <span class="text-muted small">Saat ini belum ada panggilan pasien yang sedang berlangsung di ruang dokter.</span>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold text-secondary mb-0">Daftar Antrian Kunjungan Hari Ini</h6>
                        <span class="badge bg-light text-secondary border small" id="queueSummaryBadge">
                            {{ $activeQueues->count() }} Pasien Aktif
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="small text-muted" style="width: 50px;">No</th>
                                    <th class="small text-muted">Kode Antrian</th>
                                    <th class="small text-muted">Status</th>
                                    <th class="small text-muted text-end">Waktu Daftar</th>
                                </tr>
                            </thead>
                            <tbody id="queuesTableBody">
                                @forelse ($activeQueues as $q)
                                    <tr class="{{ $q->status === 'in_progress' ? 'table-primary fw-semibold' : '' }}">
                                        <td class="mono-num text-muted">{{ $loop->iteration }}</td>
                                        <td class="mono-num fw-bold fs-6 text-primary">{{ $q->queue_code }}</td>
                                        <td>
                                            <span class="badge {{ $q->status_badge_class }}">
                                                {{ $q->status_label }}
                                            </span>
                                        </td>
                                        <td class="small text-muted text-end">{{ $q->created_at->format('H:i') }} WIB</td>
                                    </tr>
                                @empty
                                    <tr id="emptyTableRow">
                                        <td colspan="4" class="text-center py-4 text-muted small">
                                            Belum ada antrian pasien pada poliklinik ini hari ini.
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

    {{-- Script Real-Time Auto-Sync & Audio Synthesizer --}}
    <script>
        let isAudioEnabled = true;
        let lastCallingCode = @json($currentCalling ? $currentCalling->queue_code : null);
        const liveStatusUrl = @json(route('poli.live_status', $poli->slug));
        const poliName = @json($poli->name);
        const poliRoom = @json($poli->room);

        function toggleAudioCall() {
            isAudioEnabled = !isAudioEnabled;
            const btnIcon = document.getElementById('audioBtnIcon');
            const btnText = document.getElementById('audioBtnText');
            const btn = document.getElementById('toggleAudioBtn');

            if (isAudioEnabled) {
                btnIcon.className = 'bi bi-volume-up-fill me-1';
                btnText.textContent = 'Suara: Aktif';
                btn.className = 'btn btn-sm btn-outline-primary fw-semibold';
                speakText('Panggilan suara diaktifkan');
            } else {
                btnIcon.className = 'bi bi-volume-mute-fill me-1';
                btnText.textContent = 'Suara: Hening';
                btn.className = 'btn btn-sm btn-outline-secondary fw-semibold';
            }
        }

        function speakText(text) {
            if (!isAudioEnabled || !('speechSynthesis' in window)) return;
            try {
                window.speechSynthesis.cancel(); // Stop prior audio
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID';
                utterance.rate = 0.9;
                utterance.pitch = 1.0;
                window.speechSynthesis.speak(utterance);
            } catch (err) {
                console.warn('Speech synthesis error:', err);
            }
        }

        function updateCallingDisplay(currentCalling) {
            const container = document.getElementById('callingBoxContainer');
            if (currentCalling) {
                container.innerHTML = `
                    <div class="alert alert-primary d-flex align-items-center gap-3 p-4 rounded-3 mb-4 shadow-sm" role="alert" style="animation: pulseCard 1s ease;">
                        <div class="fs-1 text-primary"><i class="bi bi-megaphone-fill"></i></div>
                        <div>
                            <span class="badge bg-primary text-white text-uppercase px-2 py-1">Sedang Dipanggil</span>
                            <h1 class="mono-num fw-bold mb-0 text-primary display-4">${currentCalling.queue_code}</h1>
                            <div class="text-secondary small mt-1">Pasien dipersilakan masuk ke ruang <strong>${poliRoom}</strong>.</div>
                        </div>
                    </div>
                `;
            } else {
                container.innerHTML = `
                    <div class="p-4 bg-light rounded-3 text-center border mb-4">
                        <div class="fs-2 text-muted mb-2"><i class="bi bi-hourglass-split"></i></div>
                        <h6 class="fw-bold text-dark mb-1">Menunggu Panggilan Pasien</h6>
                        <span class="text-muted small">Saat ini belum ada panggilan pasien yang sedang berlangsung di ruang dokter.</span>
                    </div>
                `;
            }
        }

        function updateTableDisplay(queues) {
            const tbody = document.getElementById('queuesTableBody');
            const summaryBadge = document.getElementById('queueSummaryBadge');
            if (summaryBadge) {
                summaryBadge.textContent = `${queues.length} Pasien Aktif`;
            }

            if (!queues || queues.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted small">
                            Belum ada antrian pasien pada poliklinik ini hari ini.
                        </td>
                    </tr>
                `;
                return;
            }

            let html = '';
            queues.forEach((q, idx) => {
                const isCurrent = q.status === 'in_progress';
                html += `
                    <tr class="${isCurrent ? 'table-primary fw-semibold' : ''}">
                        <td class="mono-num text-muted">${idx + 1}</td>
                        <td class="mono-num fw-bold fs-6 text-primary">${q.queue_code}</td>
                        <td>
                            <span class="badge ${q.status_badge}">
                                ${q.status_label}
                            </span>
                        </td>
                        <td class="small text-muted text-end">${q.time}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        async function fetchLiveStatus() {
            try {
                const response = await fetch(liveStatusUrl, {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store'
                });
                if (!response.ok) return;

                const data = await response.json();
                const newCallingCode = data.current_calling ? data.current_calling.queue_code : null;

                // Trigger voice call when new patient is summoned
                if (newCallingCode && newCallingCode !== lastCallingCode) {
                    lastCallingCode = newCallingCode;
                    updateCallingDisplay(data.current_calling);
                    speakText(`Nomor antrian, ${newCallingCode.split('').join(' ')}, silakan menuju ${poliName}, ${poliRoom}`);
                } else if (!newCallingCode && lastCallingCode) {
                    lastCallingCode = null;
                    updateCallingDisplay(null);
                }

                updateTableDisplay(data.queues);
            } catch (err) {
                // Ignore transient network errors
            }
        }

        // Live Poll every 3 seconds for zero-latency screen updates
        setInterval(fetchLiveStatus, 3000);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
