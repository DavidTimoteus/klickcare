# KlickCare - Sistem Informasi & Antrian Poliklinik Polinema

<p align="center">
  <img src="public/img/klickCare-logo.png" width="120" alt="KlickCare Logo">
  <br>
  <strong>Layanan Fasilitas Kesehatan Terpadu Sivitas Akademika Politeknik Negeri Malang</strong>
</p>

---

## 📌 Tentang Proyek

**KlickCare** adalah sistem informasi manajemen pelayanan kesehatan dan antrian digital mandiri untuk Poliklinik Politeknik Negeri Malang (Gedung Graha Polinema Lt. 1). Sistem ini dirancang untuk menggantikan alur administrasi konvensional dengan alur digital yang efisien, transparan, dan terintegrasi dengan Nomor Induk Mahasiswa (NIM) serta NIP/NIK Dosen dan Tenaga Kependidikan.

---

## ✨ Fitur Utama Sistem

### 1. Beranda Publik & Informasi Poliklinik (`/`)
- **Indikator Operasional Real-Time**: Status buka/tutup poliklinik, jam pelayanan medis, jumlah antrian menunggu, pasien sedang diperiksa, dan pasien selesai hari ini.
- **Katalog 4 Poliklinik Spesialis**: Informasi lengkap dokter jaga, lokasi ruangan, jam praktik, dan sisa kuota harian untuk:
  - **Poli Umum** (Kode: `U`)
  - **Poli Gigi** (Kode: `G`)
  - **Poli Mata** (Kode: `M`)
  - **Poli Gizi** (Kode: `Z`)
- **Detail Layanan & Layar Antrian Publik (`/poli/{slug}`)**: Informasi dokter spesialis dan tabel panggilan antrian langsung.
- **Alur Pelayanan Pasien 3 Langkah**: Panduan pendaftaran, kedatangan, dan pemeriksaan.

### 2. Autentikasi Cerdas & Multi-Role RBAC (`/login` & `/register`)
- **Login Multi-Identifier**: Pasien dapat masuk menggunakan **Email Kampus ATAU NIM / NIP** + Kata Sandi.
- **Registrasi Pasien Mandiri**: Pendaftaran khusus mahasiswa atau staf dengan validasi integritas unik.
- **Proteksi Brute-Force & Session Security**: *Rate limiting* otomatis (maksimal 5 percobaan gagal per menit) dan *session regeneration* saat login untuk mencegah *session fixation attack*.
- **Akses Demo Cepat**: Tombol *quick-fill* pada halaman login untuk pengujian cepat antar-peran.

### 3. Portal Pasien Mandiri (`/KlickCare`)
*Khusus Mahasiswa (Role 3) & Dosen/Pegawai (Role 2)*
- **Tiket Antrian Digital Hari Ini**: Menampilkan nomor antrian besar (contoh: `U-001`), status panggilan, ruangan, dokter jaga, dan opsi pembatalan antrian mandiri.
- **Formulir Pendaftaran Antrian Baru**: Pasien memilih poli tujuan, tanggal kunjungan, dan menuliskan keluhan kesehatan utama. Sistem otomatis memvalidasi kuota dan mencegah nomor antrian ganda di hari yang sama.
- **Riwayat Pemeriksaan Medis**: Catatan kunjungan terdahulu, status diagnosa, dan catatan resep obat yang diberikan dokter.

### 4. Konsol Operasional Tenaga Medis & Admin (`/Admin-Dashboard`)
*Khusus Tenaga Medis & Administrator Poliklinik (Role 1)*
- **Dashboard KPI Klinis**: Metrik berdensitas tinggi (*tabular numerals*) untuk memantau beban antrian harian.
- **Manajemen Panggilan Antrian (*Live Queue Action*)**:
  - Panggil pasien ke ruang pemeriksaan (`in_progress`).
  - Selesaikan pemeriksaan dengan formulir diagnosis medis dan resep obat (`completed`).
  - Batalkan antrian tidak hadir (`cancelled`).
- **Filter Berdasarkan Poliklinik**: Kemudahan menyaring pasien per poli spesialis.
- **Status Kuota & Jadwal 4 Poli**: Monitoring kuota harian dokter bertugas secara terpusat.

---

## 🛠️ Arsitektur & Tech Stack

- **Backend Framework**: [Laravel 10.x](https://laravel.com/) (PHP 8.1 / 8.2 / 8.3)
- **Frontend Layer**: Laravel Blade Components + Custom *Clinical Precision Design Tokens* + Bootstrap 5.3 Grid/Utilities + Bootstrap Icons 1.11.
- **Asset Bundler**: [Vite 4/5](https://vitejs.dev/) (Kompilasi CSS/JS modern, ringan, tanpa jQuery).
- **Tipografi**: `Plus Jakarta Sans` (Display/Headings), `Inter` (Body Text), `JetBrains Mono` (Angka Tabular untuk Nomor Antrian & NIM).
- **Database**:
  - **Lokal / Testing**: SQLite (`database/database.sqlite` / `:memory:`).
  - **Produksi**: MySQL 8.0 / MariaDB (`db_klickcare`).

---

## 🚀 Panduan Instalasi & Menjalankan di Lokal

### 1. Prasyarat
- PHP >= 8.1 (dengan ekstensi `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `fileinfo`)
- Composer
- Node.js & NPM

### 2. Langkah Instalasi
```bash
# 1. Clone repositori
git clone <url-repo>
cd klickcare

# 2. Instal dependensi PHP dan Node
composer install
npm install

# 3. Siapkan berkas environment (.env)
copy .env.example .env
php artisan key:generate

# 4. Siapkan database SQLite lokal & jalankan migrasi beserta data awal (seeders)
php artisan migrate:fresh --seed

# 5. Kompilasi aset frontend
npm run build
```

### 3. Menjalankan Aplikasi
```bash
# Jalankan server lokal Laravel
php artisan serve
```
Buka browser di: `http://127.0.0.1:8000`

---

## 🔑 Akun Demo Siap Pakai

Sistem telah dilengkapi data awal (*seeders*) untuk simulasi pengujian:

| Role Pengguna | Identifier Login (Email / NIM / NIP) | Password | Akses Halaman |
| :--- | :--- | :--- | :--- |
| **Admin Medis** | `admin@admin.polinema.ac.id` | `password123` | `/Admin-Dashboard` (Konsol Pemanggilan Pasien & Rekam Medis) |
| **Mahasiswa** | `2131710012` *(atau `2131710012@student.polinema.ac.id`)* | `password123` | `/KlickCare` (Portal Tiket Antrian & Riwayat Medis) |
| **Dosen / Pegawai** | `dosen@polinema.ac.id` *(atau `197505052002121002`)* | `password123` | `/KlickCare` (Portal Pasien Dosen/Staff) |

---

## 🧪 Pengujian Otomatis (*Automated Testing*)

Proyek ini telah dilengkapi dengan rangkaian tes unit dan fitur otomatis (100% Lulus):
```bash
php artisan test
```

---

## 📄 Lisensi
Hak Cipta © Politeknik Negeri Malang. Dikembangkan untuk keperluan pelayanan kesehatan internal kampus.
