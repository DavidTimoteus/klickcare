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

        // 2. Rekam Medis Pasien Terstruktur (Electronic Medical Record)
        Schema::create('tbl_medical_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_code', 30)->unique();
            $table->foreignId('queue_id')->nullable()->constrained('tbl_queues')->nullOnDelete();
            $table->foreignId('user_id')->constrained('tbl_users')->cascadeOnDelete();
            $table->foreignId('poli_id')->constrained('tbl_polies')->cascadeOnDelete();
            $table->string('doctor_name', 100);
            $table->text('diagnosis'); // Encrypted
            $table->text('symptoms')->nullable(); // Encrypted
            $table->string('blood_pressure', 20)->nullable(); // e.g. 120/80 mmHg
            $table->string('temperature', 10)->nullable(); // e.g. 36.5 °C
            $table->string('weight', 10)->nullable(); // e.g. 62 kg
            $table->text('treatment_notes')->nullable(); // Encrypted
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
