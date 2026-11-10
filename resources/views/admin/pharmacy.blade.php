<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="{{ asset('img/klickCare-logo.png') }}" type="image/x-icon">
    <title>Manajemen Farmasi & Stok Obat - KlickCare Polinema</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/css/navfot.css', 'resources/js/app.js'])
</head>
<body style="background-color: #f1f5f9;">
    @include('components.navbar')
    @include('components.toast')

    <div class="container-fluid px-lg-4 py-4">

        {{-- Top Bar & Header --}}
        <div class="bg-white border rounded-4 p-4 mb-4 shadow-sm">
            <div class="row align-items-center justify-content-between g-3">
                <div class="col-md-7">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                            <i class="bi bi-capsule me-1"></i> Unit Farmasi & Apotek
                        </span>
                        @if ($lowStockCount > 0)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $lowStockCount }} Obat Perlu Restock
                            </span>
                        @endif
                    </div>
                    <h2 class="fw-bold mb-1">Katalog & Pengurusan Stok Obat</h2>
                    <p class="text-secondary small mb-0">
                        Kelola ketersediaan obat poliklinik, pantau batas aman minimum, dan lakukan penambahan inventaris farmasi.
                    </p>
                </div>
                <div class="col-md-5 text-md-end d-flex flex-wrap justify-content-md-end gap-2">
                    <a href="{{ route('admin') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                        <i class="bi bi-arrow-left me-1"></i> Konsol Antrian
                    </a>
                    <button type="button" class="btn btn-primary btn-sm rounded-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Obat Baru
                    </button>
                </div>
            </div>
        </div>

        {{-- Inventory KPI Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="kc-metric-card border-primary">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kc-metric-label text-primary">Total Jenis Obat</span>
                        <i class="bi bi-capsule text-primary fs-3"></i>
                    </div>
                    <div class="kc-metric-value text-primary">{{ $totalMedicinesCount }}</div>
                    <small class="text-muted">Item terdaftar dalam sistem</small>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="kc-metric-card border-warning">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kc-metric-label text-warning-emphasis">Stok Menipis</span>
                        <i class="bi bi-exclamation-diamond text-warning fs-3"></i>
                    </div>
                    <div class="kc-metric-value text-warning-emphasis">{{ $lowStockCount }}</div>
                    <small class="text-muted">&le; batas minimum stok</small>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="kc-metric-card border-danger">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kc-metric-label text-danger">Stok Habis</span>
                        <i class="bi bi-x-octagon text-danger fs-3"></i>
                    </div>
                    <div class="kc-metric-value text-danger">{{ $outOfStockCount }}</div>
                    <small class="text-muted">Perlu pengadaan segera</small>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="kc-metric-card border-success">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="kc-metric-label text-success">Kategori Farmasi</span>
                        <i class="bi bi-tags text-success fs-3"></i>
                    </div>
                    <div class="kc-metric-value text-success">{{ $categories->count() }}</div>
                    <small class="text-muted">Klasifikasi terapeutik aktif</small>
                </div>
            </div>
        </div>

        {{-- Filter & Search Bar --}}
        <div class="bg-white border rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 border-bottom pb-3 mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Daftar Inventaris Obat Poliklinik</h5>
                    <p class="text-muted small mb-0">Klik tombol restock untuk menambah jumlah unit obat yang tersedia.</p>
                </div>

                <form action="{{ route('admin.pharmacy') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" class="form-control" placeholder="Cari nama / kode..." value="{{ $search }}">
                    </div>

                    <select name="category" class="form-select form-select-sm" style="max-width: 180px;" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" {{ $selectedCategory == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>

                    @if ($search || $selectedCategory)
                        <a href="{{ route('admin.pharmacy') }}" class="btn btn-outline-secondary btn-sm" title="Reset filter">
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
                            <th class="small text-muted">Nama Obat</th>
                            <th class="small text-muted">Kategori</th>
                            <th class="small text-muted text-center">Sisa Stok</th>
                            <th class="small text-muted text-center">Batas Min</th>
                            <th class="small text-muted">Status Ketersediaan</th>
                            <th class="small text-muted">Deskripsi & Indikasi</th>
                            <th class="small text-muted text-center" style="min-width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($medicines as $m)
                            <tr>
                                <td class="mono-num fw-bold text-primary">{{ $m->code }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $m->name }}</div>
                                    <small class="text-muted">Satuan: {{ $m->unit }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $m->category }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="mono-num fw-bold fs-6 {{ $m->isOutOfStock() ? 'text-danger' : ($m->isLowStock() ? 'text-warning-emphasis' : 'text-success') }}">
                                        {{ $m->stock }}
                                    </span>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $m->unit }}</small>
                                </td>
                                <td class="text-center mono-num small text-secondary">
                                    {{ $m->min_stock }} {{ $m->unit }}
                                </td>
                                <td>
                                    @if ($m->isOutOfStock())
                                        <span class="badge bg-danger text-white">
                                            <i class="bi bi-x-circle me-1"></i> Habis
                                        </span>
                                    @elseif ($m->isLowStock())
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Menipis
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle me-1"></i> Tersedia
                                        </span>
                                    @endif
                                </td>
                                <td class="small text-secondary" style="max-width: 250px;">
                                    {{ $m->description ?? '-' }}
                                </td>
                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-sm fw-semibold rounded-2" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#restockModal"
                                            data-id="{{ $m->id }}"
                                            data-name="{{ $m->name }}"
                                            data-code="{{ $m->code }}"
                                            data-category="{{ $m->category }}"
                                            data-unit="{{ $m->unit }}"
                                            data-stock="{{ $m->stock }}"
                                            data-is-low="{{ $m->isLowStock() ? '1' : '0' }}">
                                        <i class="bi bi-plus-lg me-1"></i> Restock
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted small">
                                    <i class="bi bi-capsule fs-2 text-secondary d-block mb-2"></i>
                                    Tidak ada data obat yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($medicines->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $medicines->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Single Dynamic Modal Restock Obat (Zero-Flicker Root Level) --}}
    <div class="modal fade" id="restockModal" tabindex="-1" aria-labelledby="restockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('admin.pharmacy.stock') }}" method="POST">
                    @csrf
                    <input type="hidden" name="medicine_id" id="restockMedicineId" value="">
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold" id="restockModalLabel">
                            <i class="bi bi-capsule text-primary me-2"></i>Restock Obat: <span id="restockMedicineNameTitle" class="text-dark">-</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start p-4">
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="d-flex justify-content-between mb-1 small">
                                <span class="text-muted">Kode Obat:</span>
                                <span class="mono-num fw-bold text-dark" id="restockMedicineCode">-</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1 small">
                                <span class="text-muted">Kategori:</span>
                                <span class="fw-semibold text-dark" id="restockMedicineCategory">-</span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Stok Saat Ini:</span>
                                <span class="mono-num fw-bold" id="restockMedicineCurrentStock">-</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="restockAdditionalStockInput" class="form-label small fw-bold">
                                Jumlah Penambahan Stok (<span id="restockMedicineUnitLabel">unit</span>): <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-primary"><i class="bi bi-plus-circle"></i></span>
                                <input type="number" id="restockAdditionalStockInput" name="additional_stock" class="form-control" min="1" max="10000" placeholder="Contoh: 50" required autofocus>
                                <span class="input-group-text bg-light text-muted" id="restockMedicineUnitAddon">unit</span>
                            </div>
                            <small class="text-muted mt-1 d-block">Stok akan langsung ditambahkan ke inventaris poliklinik secara terverifikasi.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold rounded-3">
                            <i class="bi bi-check2 me-1"></i> Simpan Penambahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Obat Baru --}}
    <div class="modal fade" id="addMedicineModal" tabindex="-1" aria-labelledby="addMedicineModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('admin.pharmacy.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold" id="addMedicineModalLabel">
                            <i class="bi bi-capsule text-primary me-2"></i> Tambah Item Obat ke Katalog Farmasi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Kode Obat <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control" placeholder="Contoh: OBT-011" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Nama Obat & Dosis <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Contoh: Cetirizine 10mg" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Kategori / Golongan <span class="text-danger">*</span></label>
                                <input type="text" name="category" class="form-control" placeholder="Contoh: Antihistamin, Analgesik, Antibiotik" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Satuan <span class="text-danger">*</span></label>
                                <input type="text" name="unit" class="form-control" placeholder="Contoh: tablet, sirup, strip, botol, kapsul" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Stok Awal <span class="text-danger">*</span></label>
                                <input type="number" name="stock" class="form-control" min="0" value="50" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Batas Minimum Stok <span class="text-danger">*</span></label>
                                <input type="number" name="min_stock" class="form-control" min="1" value="15" required>
                                <small class="text-muted">Sistem memberi peringatan jika stok mencapai nilai ini.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Deskripsi, Indikasi & Efek Samping</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Contoh: Mengatasi reaksi alergi, gatal-gatal, dan rhinitis alergi. Dosis umum 1x sehari."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold rounded-3">
                            <i class="bi bi-save me-1"></i> Simpan Obat Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const restockModal = document.getElementById('restockModal');
            if (restockModal) {
                restockModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (!button) return;

                    const id = button.getAttribute('data-id');
                    const name = button.getAttribute('data-name');
                    const code = button.getAttribute('data-code');
                    const category = button.getAttribute('data-category');
                    const unit = button.getAttribute('data-unit');
                    const stock = button.getAttribute('data-stock');
                    const isLow = button.getAttribute('data-is-low') === '1';

                    document.getElementById('restockMedicineId').value = id || '';
                    document.getElementById('restockMedicineNameTitle').textContent = name || '-';
                    document.getElementById('restockMedicineCode').textContent = code || '-';
                    document.getElementById('restockMedicineCategory').textContent = category || '-';
                    
                    const stockEl = document.getElementById('restockMedicineCurrentStock');
                    stockEl.textContent = (stock || '0') + ' ' + (unit || '');
                    stockEl.className = 'mono-num fw-bold ' + (isLow ? 'text-danger' : 'text-success');

                    document.getElementById('restockMedicineUnitLabel').textContent = unit || 'unit';
                    document.getElementById('restockMedicineUnitAddon').textContent = unit || 'unit';
                    
                    const inputEl = document.getElementById('restockAdditionalStockInput');
                    inputEl.value = '';
                });
            }
        });
    </script>
</body>
</html>

