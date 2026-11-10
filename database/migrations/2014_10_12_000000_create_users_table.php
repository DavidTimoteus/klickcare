<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('noId', 30)->unique(); // NIM (Mahasiswa) atau NIP/NIK (Dosen/Pegawai/Admin)
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->enum('gender', ['Laki-Laki', 'Perempuan'])->nullable();
            $table->date('birthDate')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('phoneNumber', 30)->nullable();
            $table->string('medical_record_number', 30)->nullable()->unique(); // Nomor Rekam Medis (No. RM) Unik Pasien Seumur Hidup (Permenkes 24/2022)
            $table->string('allergies', 255)->nullable()->default('Tidak ada riwayat alergi obat'); // Riwayat Alergi Obat Pasien (Safety Alert)
            $table->string('blood_type', 5)->nullable(); // Golongan Darah (A, B, AB, O)
            $table->unsignedSmallInteger('role')->default(3); // 1 = Admin/Petugas, 2 = Dosen/Pegawai, 3 = Mahasiswa
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_users');
    }
};
