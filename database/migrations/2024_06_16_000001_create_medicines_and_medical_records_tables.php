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
        // 1. Katalog Stok Obat & Farmasi Poliklinik
        Schema::create('tbl_medicines', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->string('category', 50); // Analgesik, Antibiotik, Vitamin, Antasida, dll.
            $table->string('unit', 30); // Tablet, Kapsul, Botol, Strip, Tube, Sachet
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(10);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'is_active']);
        });

        // 2. Rekam Medis Pasien Terstruktur (Electronic Medical Record - Sesuai Permenkes 24/2022 & Standar SOAP KKI)
        Schema::create('tbl_medical_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_code', 30)->unique(); // Kode Kunjungan / Encounter ID (e.g. ENC-202611-0001)
            $table->foreignId('queue_id')->nullable()->constrained('tbl_queues')->nullOnDelete();
            $table->foreignId('user_id')->constrained('tbl_users')->cascadeOnDelete();
            $table->foreignId('poli_id')->constrained('tbl_polies')->cascadeOnDelete();
            $table->string('doctor_name', 100);
            $table->string('doctor_sip', 50)->nullable(); // Surat Izin Praktik DPJP

            // S - Subjective (Anamnesis & Riwayat Alergi)
            $table->text('symptoms')->nullable(); // Keluhan Utama & RPS (Encrypted)
            $table->string('allergies_checked', 255)->nullable(); // Konfirmasi Alergi Obat saat Pemeriksaan

            // O - Objective (Pemeriksaan Fisik & Tanda Vital TTV Standar)
            $table->string('blood_pressure', 20)->nullable(); // Tekanan Darah (mmHg) e.g. 120/80
            $table->string('heart_rate', 10)->nullable(); // Denyut Nadi (bpm) e.g. 80
            $table->string('respiratory_rate', 10)->nullable(); // Frekuensi Nafas (x/menit) e.g. 18
            $table->string('temperature', 10)->nullable(); // Suhu Tubuh (°C) e.g. 36.5
            $table->string('weight', 10)->nullable(); // Berat Badan (kg) e.g. 62
            $table->string('height', 10)->nullable(); // Tinggi Badan (cm) e.g. 170
            $table->text('physical_exam')->nullable(); // Pemeriksaan Fisik Khusus (Encrypted)

            // A - Assessment (Diagnosis Standar ICD-10)
            $table->text('diagnosis'); // Diagnosis Primer / Kerja (Encrypted)
            $table->string('icd10_code', 20)->nullable(); // Kode ICD-10 (e.g. J02.9, K29.7, K04.0)

            // P - Plan (Tata Laksana, Tindakan Medis & Edukasi Pasien)
            $table->text('treatment_notes')->nullable(); // Rencana Terapi & Tindakan Medis (Encrypted)
            $table->text('follow_up_advice')->nullable(); // Anjuran Istirahat / Rujukan / Kontrol Ulang

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // 3. Rincian Resep Obat (Prescription Items)
        Schema::create('tbl_prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('tbl_medical_records')->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained('tbl_medicines')->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->string('dosage', 100); // e.g. '3 x 1 tablet sesudah makan'
            $table->string('instructions', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_prescription_items');
        Schema::dropIfExists('tbl_medical_records');
        Schema::dropIfExists('tbl_medicines');
    }
};
