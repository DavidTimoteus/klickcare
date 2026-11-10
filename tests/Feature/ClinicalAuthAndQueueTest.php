<?php

namespace Tests\Feature;

use App\Models\Poli;
use App\Models\Queue;
use App\Models\User;
use Database\Seeders\PoliSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicalAuthAndQueueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([UserSeeder::class, PoliSeeder::class]);
    }

    public function test_landing_page_loads_with_active_polies(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('KlickCare');
        $response->assertSee('Poli Umum');
        $response->assertSee('Poli Gigi');
        $response->assertSee('Poli Mata');
        $response->assertSee('Poli Gizi');
    }

    public function test_poli_detail_page_loads(): void
    {
        $response = $this->get('/poli/poliumum');

        $response->assertStatus(200);
        $response->assertSee('Poli Umum');
        $response->assertSee('dr. H. Hendra Setiawan');
    }

    public function test_guest_is_redirected_from_portal_and_admin_pages(): void
    {
        $this->get('/KlickCare')->assertRedirect('/login');
        $this->get('/Admin-Dashboard')->assertRedirect('/login');
    }

    public function test_admin_can_login_and_access_admin_dashboard(): void
    {
        $response = $this->post('/login', [
            'login' => 'admin@admin.polinema.ac.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/Admin-Dashboard');
        $this->assertAuthenticated();

        $adminResponse = $this->get('/Admin-Dashboard');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Manajemen Operasional Poliklinik');
    }

    public function test_student_can_login_with_nim_and_access_patient_portal(): void
    {
        $response = $this->post('/login', [
            'login' => '2131710012', // NIM
            'password' => 'password123',
        ]);

        $response->assertRedirect('/KlickCare');
        $this->assertAuthenticated();

        $portalResponse = $this->get('/KlickCare');
        $portalResponse->assertStatus(200);
        $portalResponse->assertSee('David Timoteus');
        $portalResponse->assertSee('2131710012');
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $student = User::where('role', User::ROLE_STUDENT)->first();

        $this->actingAs($student)
            ->get('/Admin-Dashboard')
            ->assertRedirect('/KlickCare');
    }

    public function test_patient_can_register_new_queue_ticket(): void
    {
        $student = User::where('role', User::ROLE_STUDENT)->first();
        $poliGizi = Poli::where('slug', 'poligizi')->first();

        $response = $this->actingAs($student)->post('/antrian/daftar', [
            'poli_id' => $poliGizi->id,
            'visit_date' => today()->format('Y-m-d'),
            'complaint' => 'Konsultasi pola makan untuk pemulihan stamina',
        ]);

        $response->assertRedirect('/KlickCare');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tbl_queues', [
            'user_id' => $student->id,
            'poli_id' => $poliGizi->id,
            'queue_code' => 'Z-001',
            'status' => 'waiting',
        ]);
    }

    public function test_admin_can_update_queue_status_and_complete_examination(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $queue = Queue::where('status', 'waiting')->first();

        // 1. Panggil pasien (in_progress)
        $this->actingAs($admin)->post("/admin/antrian/{$queue->id}/status", [
            'status' => 'in_progress',
        ])->assertSessionHas('success');

        $this->assertEquals('in_progress', $queue->fresh()->status);

        // 2. Selesaikan pemeriksaan dengan catatan medis (completed)
        $this->actingAs($admin)->post("/admin/antrian/{$queue->id}/status", [
            'status' => 'completed',
            'doctor_notes' => 'Diberikan Paracetamol 500mg 3x1 dan istirahat cukup 2 hari.',
        ])->assertSessionHas('success');

        $this->assertEquals('completed', $queue->fresh()->status);
        $this->assertStringContainsString('Paracetamol', $queue->fresh()->doctor_notes);
    }

    public function test_registration_rejects_non_campus_emails(): void
    {
        $response = $this->post('/register', [
            'name' => 'Intruder',
            'noId' => '9999999999',
            'role' => '3',
            'email' => 'intruder@gmail.com', // Non-campus email
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_single_active_in_progress_patient_enforced_per_poli(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $poli = Poli::where('slug', 'poliumum')->first();

        // Cari antrian pertama yang sedang in_progress
        $firstQueue = Queue::where('poli_id', $poli->id)->where('status', 'in_progress')->first();
        $this->assertNotNull($firstQueue);

        // Buat antrian kedua di poli yang sama
        $secondStudent = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'email' => 'secondstudent@student.polinema.ac.id',
            'noId' => '2131700999',
        ]);

        $secondQueue = Queue::create([
            'user_id' => $secondStudent->id,
            'poli_id' => $poli->id,
            'queue_code' => 'U-099',
            'queue_number' => 99,
            'visit_date' => today()->format('Y-m-d'),
            'complaint' => 'Pusing kepala',
            'status' => 'waiting',
        ]);

        // Coba panggil antrian kedua saat antrian pertama masih in_progress
        $response = $this->actingAs($admin)->post("/admin/antrian/{$secondQueue->id}/status", [
            'status' => 'in_progress',
        ]);

        // Harus ditolak dengan pesan error
        $response->assertSessionHasErrors(['queue']);
        $this->assertEquals('waiting', $secondQueue->fresh()->status);
    }

    public function test_queue_skipping_and_recall_lifecycle(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $activeQueue = Queue::where('status', 'in_progress')->firstOrFail();

        // 1. Dokter melewati pasien karena belum datang (skipped)
        $this->actingAs($admin)->post("/admin/antrian/{$activeQueue->id}/status", [
            'status' => 'skipped',
        ])->assertSessionHas('success');

        $this->assertEquals('skipped', $activeQueue->fresh()->status);

        // 2. Dokter memanggil ulang pasien yang dilewati (recall to in_progress)
        $this->actingAs($admin)->post("/admin/antrian/{$activeQueue->id}/status", [
            'status' => 'in_progress',
        ])->assertSessionHas('success');

        $this->assertEquals('in_progress', $activeQueue->fresh()->status);
    }

    public function test_live_poli_status_api_endpoint(): void
    {
        $response = $this->getJson('/poli/poliumum/live-status');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'poli_name',
            'room',
            'remaining_quota',
            'current_calling',
            'waiting_count',
            'skipped_count',
            'queues',
            'timestamp',
        ]);
        $response->assertJson(['poli_name' => 'Poli Umum']);
    }

    public function test_database_enforces_unique_daily_poli_queue_constraint(): void
    {
        $student = User::where('role', User::ROLE_STUDENT)->first();
        $poli = Poli::where('slug', 'poliumum')->first();
        $date = today()->format('Y-m-d');

        // Buat antrian dengan nomor 88
        Queue::create([
            'user_id' => $student->id,
            'poli_id' => $poli->id,
            'queue_code' => 'U-088',
            'queue_number' => 88,
            'visit_date' => $date,
            'complaint' => 'Keluhan pertama',
            'status' => 'waiting',
        ]);

        // Coba masukkan antrian lain dengan nomor 88 pada tanggal dan poli yang sama
        $this->expectException(\Illuminate\Database\QueryException::class);

        Queue::create([
            'user_id' => $student->id,
            'poli_id' => $poli->id,
            'queue_code' => 'U-088',
            'queue_number' => 88,
            'visit_date' => $date,
            'complaint' => 'Keluhan kedua duplikat',
            'status' => 'waiting',
        ]);
    }
}
