<?php

namespace Tests\Feature;

use App\Models\Poli;
use App\Models\Queue;
use App\Models\User;
use Database\Seeders\PoliSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EndToEndClinicalFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([UserSeeder::class, PoliSeeder::class]);
    }

    /**
     * E2E: Full Patient Journey
     * Landing -> Registration -> Portal -> Booking Queue -> Check Queue -> Logout without 419
     */
    public function test_complete_patient_journey_e2e(): void
    {
        // 1. Visit Landing Page
        $landing = $this->get('/');
        $landing->assertStatus(200);
        $landing->assertSee('KlickCare');
        $landing->assertSee('Poli Umum');

        // 2. Register as a new Student
        $registerResponse = $this->post('/register', [
            'name' => 'Ahmad Fauzi',
            'noId' => '2141762099',
            'role' => '3', // Mahasiswa
            'email' => 'ahmad.fauzi@student.polinema.ac.id',
            'gender' => 'Laki-Laki',
            'phoneNumber' => '081299887766',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $registerResponse->assertRedirect('/KlickCare');
        $this->assertAuthenticated();

        // 3. View Patient Portal
        $portalResponse = $this->get('/KlickCare');
        $portalResponse->assertStatus(200);
        $portalResponse->assertSee('Ahmad Fauzi');
        $portalResponse->assertSee('2141762099');

        // 4. Book a Queue Ticket for Poli Gigi
        $poliGigi = Poli::where('slug', 'poligigi')->first();
        $bookResponse = $this->post('/antrian/daftar', [
            'poli_id' => $poliGigi->id,
            'visit_date' => today()->format('Y-m-d'),
            'complaint' => 'Pemeriksaan sakit gigi geraham berlubang',
        ]);

        $bookResponse->assertRedirect('/KlickCare');
        $bookResponse->assertSessionHas('success');

        // Check active ticket on dashboard with verified encryption
        $this->assertDatabaseHas('tbl_queues', [
            'poli_id' => $poliGigi->id,
            'status' => 'waiting',
        ]);
        $studentUser = User::where('email', 'ahmad.fauzi@student.polinema.ac.id')->firstOrFail();
        $createdQueue = Queue::where('poli_id', $poliGigi->id)->where('user_id', $studentUser->id)->firstOrFail();
        $this->assertEquals('Pemeriksaan sakit gigi geraham berlubang', $createdQueue->complaint);

        // Verify raw database row is encrypted (not plaintext)
        $rawComplaint = \Illuminate\Support\Facades\DB::table('tbl_queues')->where('id', $createdQueue->id)->value('complaint');
        $this->assertNotEquals('Pemeriksaan sakit gigi geraham berlubang', $rawComplaint);

        $portalAfterBooking = $this->get('/KlickCare');
        $portalAfterBooking->assertStatus(200);
        $portalAfterBooking->assertSee('G-002'); // Next after seeded G-001
        $portalAfterBooking->assertSee('Menunggu Panggilan');

        // 5. Test Logout via POST without 419 error
        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();

        // 6. Test Logout via GET (fallback) without 419 error
        $getLogoutResponse = $this->get('/logout');
        $getLogoutResponse->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * E2E: Full Admin Clinical Workflow Journey
     * Admin Login -> Console -> Call Patient -> Complete with Prescription -> Logout
     */
    public function test_complete_admin_clinical_workflow_e2e(): void
    {
        // 1. Login as Admin
        $loginResponse = $this->post('/login', [
            'login' => 'admin@admin.polinema.ac.id',
            'password' => 'password123',
        ]);

        $loginResponse->assertRedirect('/Admin-Dashboard');
        $this->assertAuthenticated();

        // 2. View Console
        $console = $this->get('/Admin-Dashboard');
        $console->assertStatus(200);
        $console->assertSee('Manajemen Operasional Poliklinik');

        // Find existing waiting queue
        $queue = Queue::where('status', 'waiting')->firstOrFail();

        // 3. Call patient to examination room (in_progress)
        $callResponse = $this->post("/admin/antrian/{$queue->id}/status", [
            'status' => 'in_progress',
        ]);
        $callResponse->assertSessionHas('success');
        $this->assertEquals('in_progress', $queue->fresh()->status);

        // 4. Complete examination with doctor notes and prescription
        $completeResponse = $this->post("/admin/antrian/{$queue->id}/status", [
            'status' => 'completed',
            'doctor_notes' => 'Diagnosis: Pulpitis Reversibel. Resep: Amoxicillin 500mg 3x1, Asam Mefenamat 500mg jika nyeri.',
        ]);
        $completeResponse->assertSessionHas('success');

        $freshQueue = $queue->fresh();
        $this->assertEquals('completed', $freshQueue->status);
        $this->assertStringContainsString('Amoxicillin', $freshQueue->doctor_notes);

        // 5. Admin Logout without 419
        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_patient_can_cancel_queue_and_quota_is_released(): void
    {
        $freshStudent = User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'noId' => '2131799999',
            'email' => 'freshstudent@student.polinema.ac.id',
        ]);
        $poli = Poli::where('slug', 'polimata')->firstOrFail();

        $initialRemaining = $poli->remainingQuotaToday();

        $this->actingAs($freshStudent);

        // Pasien daftar antrian
        $regResponse = $this->post('/antrian/daftar', [
            'poli_id' => $poli->id,
            'complaint' => 'Demam dan sakit tenggorokan sejak kemarin malam',
        ]);
        $regResponse->assertRedirect('/KlickCare');
        $this->assertEquals($initialRemaining - 1, $poli->fresh()->remainingQuotaToday());

        // Cek dashboard portal pasien
        $portalResponse = $this->get('/KlickCare');
        $portalResponse->assertStatus(200);
        $portalResponse->assertSee('Tiket Antrian Digital Anda');
        $portalResponse->assertSee('Pantau Layar Poli');
        $portalResponse->assertSee('Batal');

        $activeQueue = Queue::where('user_id', $freshStudent->id)
            ->where('poli_id', $poli->id)
            ->where('status', 'waiting')
            ->firstOrFail();

        // Pasien batalkan antrian
        $cancelResponse = $this->post("/antrian/{$activeQueue->id}/batal");
        $cancelResponse->assertRedirect();
        $cancelResponse->assertSessionHas('info');

        // Kuota kembali pulih
        $this->assertEquals('cancelled', $activeQueue->fresh()->status);
        $this->assertEquals($initialRemaining, $poli->fresh()->remainingQuotaToday());
    }
}

