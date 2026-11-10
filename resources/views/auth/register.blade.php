@extends('auth.index')

@section('title', 'Pendaftaran Pasien - KlickCare Polinema')

@section('content')
<div class="kc-auth-wrapper">
    <div class="kc-auth-card" style="max-width: 540px;">
        <div class="text-center mb-4">
            <img src="{{ asset('img/klickCare-logo.png') }}" alt="KlickCare" height="48" class="mb-2">
            <h2 class="kc-auth-title">Registrasi Pasien Baru</h2>
            <p class="kc-auth-subtitle">Pendaftaran Sivitas Akademika Politeknik Negeri Malang</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 rounded-3 small mb-3">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf

            <div class="kc-form-group">
                <label for="name" class="kc-form-label">Nama Lengkap Pasien</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" placeholder="contoh: Muhammad Rizki" required>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-sm-6">
                    <label for="noId" class="kc-form-label">NIM / NIP / NIK</label>
                    <input type="text" class="form-control @error('noId') is-invalid @enderror" 
                           id="noId" name="noId" value="{{ old('noId') }}" placeholder="contoh: 2141720099" required>
                </div>
                <div class="col-sm-6">
                    <label for="role" class="kc-form-label">Kategori Pengguna</label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="3" {{ old('role') == '3' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="2" {{ old('role') == '2' ? 'selected' : '' }}>Dosen / Tendik</option>
                    </select>
                </div>
            </div>

            <div class="kc-form-group">
                <label for="email" class="kc-form-label">Email Kampus Polinema</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" placeholder="contoh: nama@student.polinema.ac.id" required>
                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Gunakan email resmi berakhiran @student.polinema.ac.id atau @polinema.ac.id</small>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-sm-6">
                    <label for="gender" class="kc-form-label">Jenis Kelamin</label>
                    <select class="form-select" id="gender" name="gender">
                        <option value="Laki-Laki" {{ old('gender') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                        <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="col-sm-6">
                    <label for="phoneNumber" class="kc-form-label">Nomor WhatsApp / HP</label>
                    <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" value="{{ old('phoneNumber') }}" placeholder="08xxxxxxxxxx">
                </div>
            </div>

            <div class="row g-2 mb-4">
                <div class="col-sm-6">
                    <label for="password" class="kc-form-label">Kata Sandi</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" placeholder="Minimal 6 karakter" required>
                </div>
                <div class="col-sm-6">
                    <label for="password_confirmation" class="kc-form-label">Konfirmasi Sandi</label>
                    <input type="password" class="form-control" 
                           id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi" required>
                </div>
            </div>

            <button type="submit" class="btn btn-kc-primary w-100 py-2 fw-bold shadow-sm">
                Daftar & Masuk ke Portal
            </button>

            <div class="text-center mt-3 small">
                <span class="text-muted">Sudah memiliki akun?</span>
                <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none ms-1">Masuk Sekarang</a>
            </div>
        </form>
    </div>
</div>
@endsection
