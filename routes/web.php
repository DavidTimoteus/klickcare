<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - KlickCare (E-Poliklinik Politeknik Negeri Malang)
|--------------------------------------------------------------------------
*/

// Halaman Publik (Beranda & Informasi Poliklinik)
Route::get('/', [DashboardController::class, 'landing'])->name('home');
Route::get('/poli/{slug}', [DashboardController::class, 'poliDetail'])->name('poli.detail');
Route::get('/poli/{slug}/live-status', [DashboardController::class, 'livePoliStatus'])->name('poli.live_status');

// Rute Autentikasi Pengguna (Khusus Tamu / Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/register', [LoginController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [LoginController::class, 'register'])->name('register.post');
});

// Logout (Pengguna Terautentikasi - mendukung POST & GET tanpa kendala 419)
Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

// Portal Pasien (Mahasiswa [Role 3] & Dosen/Pegawai [Role 2])
Route::middleware(['auth', 'role:2,3'])->group(function () {
    Route::get('/KlickCare', [DashboardController::class, 'userDashboard'])->name('user');
    Route::post('/antrian/daftar', [DashboardController::class, 'storeQueue'])->name('queue.store');
    Route::post('/antrian/{queue}/batal', [DashboardController::class, 'cancelQueue'])->name('queue.cancel');
});

// Konsol Admin & Tenaga Medis (Role 1)
Route::middleware(['auth', 'role:1'])->group(function () {
    Route::get('/Admin-Dashboard', [DashboardController::class, 'adminDashboard'])->name('admin');
    Route::post('/admin/antrian/{queue}/status', [DashboardController::class, 'updateQueueStatus'])->name('admin.queue.status');
});
