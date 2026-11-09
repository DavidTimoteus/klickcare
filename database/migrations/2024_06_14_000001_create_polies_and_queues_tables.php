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
        Schema::create('tbl_polies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->string('code_prefix', 5)->default('A');
            $table->string('doctor_name', 100);
            $table->string('room', 100);
            $table->string('open_time', 10)->default('08:00');
            $table->string('close_time', 10)->default('15:30');
            $table->unsignedInteger('daily_quota')->default(30);
            $table->string('icon_path', 255);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tbl_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('tbl_users')->cascadeOnDelete();
            $table->foreignId('poli_id')->constrained('tbl_polies')->cascadeOnDelete();
            $table->string('queue_code', 20); // e.g. U-001, G-002, M-003, Z-004
            $table->unsignedInteger('queue_number');
            $table->date('visit_date');
            $table->text('complaint');
            $table->enum('status', ['waiting', 'in_progress', 'completed', 'cancelled'])->default('waiting');
            $table->text('doctor_notes')->nullable();
            $table->timestamps();

            $table->index(['visit_date', 'poli_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_queues');
        Schema::dropIfExists('tbl_polies');
    }
};
