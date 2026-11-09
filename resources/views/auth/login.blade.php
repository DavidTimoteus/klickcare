@extends('auth.index')

@section('title', 'Masuk - KlickCare Polinema')

@section('content')
<div class="kc-auth-wrapper">
    <div class="kc-auth-container">
        <div class="row g-0">
            {{-- Left Side: Form Login --}}
            <div class="col-lg-6">
                <div class="kc-auth-form-side">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="{{ asset('img/klickCare-logo.png') }}" alt="KlickCare" height="42">
                        <span class="fw-bold fs-4 text-primary">KlickCare</span>
                    </div>

                    <h2 class="kc-auth-title">Masuk ke Portal</h2>
                    <p class="kc-auth-subtitle">Gunakan Email Kampus atau NIM / NIP Polinema Anda</p>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 px-3 rounded-3 small mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf

                        <div class="kc-form-group">
                            <label for="login" class="kc-form-label">Email Kampus / NIM / NIP</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0 @error('login') is-invalid @enderror" 
                                       id="login" name="login" value="{{ old('login') }}" 
                                       placeholder="contoh: 2131710012 atau admin@admin.polinema.ac.id" required autofocus>
                            </div>
                        </div>

                        <div class="kc-form-group">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="kc-form-label mb-0">Kata Sandi</label>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                                       id="password" name="password" placeholder="Masukkan kata sandi" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small text-secondary" for="remember">
                                    Ingat Saya
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-kc-primary w-100 py-2 fw-bold shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                        </button>

                        <div class="text-center mt-3 small">
                            <span class="text-muted">Belum memiliki akun?</span>
                            <a href="{{ route('register') }}" class="fw-bold text-primary text-decoration-none ms-1">Daftar Akun Pasien</a>
                        </div>
                    </form>

                    {{-- Demo Accounts Quick-Fill Helper for Testing & Evaluation --}}
                    <div class="kc-demo-accounts">
                        <div class="fw-bold text-dark mb-1 d-flex align-items-center gap-1">
                            <i class="bi bi-key-fill text-warning"></i>
                            <span>Akses Akun Demo Cepat (Klik untuk isi otomatis):</span>
                        </div>
                        <button type="button" class="kc-demo-btn" onclick="fillCredentials('admin@admin.polinema.ac.id', 'password123')">
                            <strong>Admin Medis:</strong> admin@admin.polinema.ac.id
                        </button>
                        <button type="button" class="kc-demo-btn" onclick="fillCredentials('2131710012@student.polinema.ac.id', 'password123')">
                            <strong>Mahasiswa:</strong> 2131710012 (David Timoteus)
                        </button>
                        <button type="button" class="kc-demo-btn" onclick="fillCredentials('dosen@polinema.ac.id', 'password123')">
                            <strong>Dosen / Staff:</strong> dosen@polinema.ac.id (Ir. Bambang Wijaya)
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right Side: Visual Showcase with Real Photo & Trust Badges --}}
            <div class="col-lg-6 d-none d-lg-block">
                <div class="kc-auth-side-banner">
                    <div>
                        <span class="badge bg-white text-primary px-3 py-1 fw-bold mb-3">Poliklinik Polinema</span>
                        <h3 class="fw-bold text-white mb-2">Pelayanan Kesehatan Cepat, Nyaman, dan Terintegrasi</h3>
                        <p class="text-light opacity-75 small">
                            Ambil nomor antrian tanpa perlu berdesakan. Pantau giliran periksa Anda secara langsung dari ponsel Anda.
                        </p>
                    </div>

                    <div class="text-center my-3">
                        <img src="{{ asset('img/side-login.png') }}" class="kc-auth-side-img" alt="Ilustrasi Kesehatan Polinema">
                    </div>

                    <div class="p-3 bg-white bg-opacity-10 rounded-3 border border-light border-opacity-25 small">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-clock-history text-warning"></i>
                            <span class="fw-semibold text-white">Jam Praktik Poliklinik:</span>
                        </div>
                        <span class="text-light opacity-75">Senin - Kamis (08:00 - 15:30) · Jumat (08:00 - 15:00)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function fillCredentials(identifier, password) {
        document.getElementById('login').value = identifier;
        document.getElementById('password').value = password;
    }
</script>
@endpush
