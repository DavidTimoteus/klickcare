@extends('auth.index')

@section('title', 'Masuk - KlickCare Polinema')

@section('content')
<div class="kc-auth-wrapper">
    <div class="kc-auth-card">
        <div class="text-center mb-4">
            <img src="{{ asset('img/klickCare-logo.png') }}" alt="KlickCare" height="48" class="mb-2">
            <h2 class="kc-auth-title">Masuk ke KlickCare</h2>
            <p class="kc-auth-subtitle">Gunakan Email Kampus atau NIM / NIP Polinema</p>
        </div>

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
                Masuk Sekarang
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
                <strong>Admin Medis:</strong> admin@admin.polinema.ac.id (Pass: password123)
            </button>
            <button type="button" class="kc-demo-btn" onclick="fillCredentials('2131710012@student.polinema.ac.id', 'password123')">
                <strong>Mahasiswa:</strong> 2131710012 (Pass: password123)
            </button>
            <button type="button" class="kc-demo-btn" onclick="fillCredentials('dosen@polinema.ac.id', 'password123')">
                <strong>Dosen / Staff:</strong> dosen@polinema.ac.id (Pass: password123)
            </button>
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
