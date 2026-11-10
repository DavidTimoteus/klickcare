<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Poli;
use App\Models\PrescriptionItem;
use App\Models\Queue;
use App\Models\User;
use Database\Seeders\MedicineSeeder;
use Database\Seeders\PoliSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PharmacyAndMedicalRecordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([UserSeeder::class, PoliSeeder::class, MedicineSeeder::class]);
    }

    public function test_admin_can_access_pharmacy_dashboard(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($admin)->get('/admin/farmasi');

        $response->assertStatus(200);
        $response->assertSee('Katalog & Pengurusan Stok Obat', false);
        $response->assertSee('Paracetamol 500mg');
        $response->assertSee('Amoxicillin 500mg');
        $response->assertSee('Antasida Doen');
    }

    public function test_patient_cannot_access_pharmacy_dashboard(): void
    {
        $student = User::where('role', User::ROLE_STUDENT)->first();

        $response = $this->actingAs($student)->get('/admin/farmasi');

        $response->assertRedirect('/KlickCare');
    }

    public function test_admin_can_restock_medicine(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $medicine = Medicine::where('code', 'OBT-001')->first(); // Paracetamol, initial 120

        $initialStock = $medicine->stock;

        $response = $this->actingAs($admin)->post('/admin/farmasi/stok', [
            'medicine_id' => $medicine->id,
            'additional_stock' => 50,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals($initialStock + 50, $medicine->fresh()->stock);
    }

    public function test_admin_can_register_new_medicine(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($admin)->post('/admin/farmasi/obat', [
            'code' => 'OBT-999',
            'name' => 'Vitamin C 500mg',
            'category' => 'Suplemen & Vitamin',
            'unit' => 'tablet',
            'stock' => 150,
            'min_stock' => 30,
            'description' => 'Meningkatkan daya tahan tubuh civitas akademika.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tbl_medicines', [
            'code' => 'OBT-999',
            'name' => 'Vitamin C 500mg',
            'stock' => 150,
        ]);
    }

    public function test_completing_examination_creates_medical_record_and_deducts_stock(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $student = User::where('role', User::ROLE_STUDENT)->first();
        $poliUmum = Poli::where('slug', 'poliumum')->first();
        $paracetamol = Medicine::where('code', 'OBT-001')->first();
        $amoxicillin = Medicine::where('code', 'OBT-002')->first();

        $initialParacetamolStock = $paracetamol->stock;
        $initialAmoxicillinStock = $amoxicillin->stock;

        // Bersihkan queue sample agar deterministik
        Queue::query()->delete();

        // Pasien mendaftar antrian
        $queue = Queue::create([
            'user_id' => $student->id,
            'poli_id' => $poliUmum->id,
            'queue_code' => 'U-001',
            'queue_number' => 1,
            'visit_date' => today()->format('Y-m-d'),
            'complaint' => 'Demam tinggi dan sakit tenggorokan sejak 2 hari yang lalu',
            'status' => 'in_progress',
        ]);

        // Dokter menyelesaikan pemeriksaan & meresepkan 2 obat
        $response = $this->actingAs($admin)->post("/admin/antrian/{$queue->id}/status", [
            'status' => 'completed',
            'diagnosis' => 'Faringitis Akut & Febris',
            'symptoms' => 'Nyeri menelan dan demam 38.5 C',
            'blood_pressure' => '120/80',
            'temperature' => '38.5',
            'weight' => '62',
            'doctor_notes' => 'Banyak minum air hangat dan istirahat cukup selama 3 hari.',
            'medicines' => [
                [
                    'id' => $paracetamol->id,
                    'quantity' => 10,
                    'dosage' => '3x1 tablet sesudah makan',
                ],
                [
                    'id' => $amoxicillin->id,
                    'quantity' => 15,
                    'dosage' => '3x1 tablet tiap 8 jam dihabiskan',
                ],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan queue berstatus completed
        $this->assertEquals('completed', $queue->fresh()->status);

        // Pastikan stok obat berkurang secara akurat
        $this->assertEquals($initialParacetamolStock - 10, $paracetamol->fresh()->stock);
        $this->assertEquals($initialAmoxicillinStock - 15, $amoxicillin->fresh()->stock);

        // Pastikan rekam medis terbit
        $medicalRecord = MedicalRecord::where('queue_id', $queue->id)->first();
        $this->assertNotNull($medicalRecord);
        $this->assertEquals('Faringitis Akut & Febris', $medicalRecord->diagnosis);
        $this->assertEquals('120/80', $medicalRecord->blood_pressure);
        $this->assertEquals('38.5', $medicalRecord->temperature);
        $this->assertEquals('62', $medicalRecord->weight);

        // Pastikan item resep tercatat
        $this->assertDatabaseHas('tbl_prescription_items', [
            'medical_record_id' => $medicalRecord->id,
            'medicine_id' => $paracetamol->id,
            'quantity' => 10,
            'dosage' => '3x1 tablet sesudah makan',
        ]);

        $this->assertDatabaseHas('tbl_prescription_items', [
            'medical_record_id' => $medicalRecord->id,
            'medicine_id' => $amoxicillin->id,
            'quantity' => 15,
            'dosage' => '3x1 tablet tiap 8 jam dihabiskan',
        ]);
    }

    public function test_prescription_fails_if_medicine_stock_is_insufficient(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $student = User::where('role', User::ROLE_STUDENT)->first();
        $poliUmum = Poli::where('slug', 'poliumum')->first();
        $paracetamol = Medicine::where('code', 'OBT-001')->first();

        $initialStock = $paracetamol->stock;

        // Bersihkan queue sample agar deterministik
        Queue::query()->delete();

        $queue = Queue::create([
            'user_id' => $student->id,
            'poli_id' => $poliUmum->id,
            'queue_code' => 'U-001',
            'queue_number' => 1,
            'visit_date' => today()->format('Y-m-d'),
            'complaint' => 'Sakit kepala hebat',
            'status' => 'in_progress',
        ]);

        // Coba resepkan obat melebihi stok yang ada
        $excessiveQuantity = $initialStock + 100;

        $response = $this->actingAs($admin)->post("/admin/antrian/{$queue->id}/status", [
            'status' => 'completed',
            'diagnosis' => 'Cephalea',
            'medicines' => [
                [
                    'id' => $paracetamol->id,
                    'quantity' => $excessiveQuantity,
                    'dosage' => '3x1 tablet',
                ],
            ],
        ]);

        $response->assertSessionHasErrors(['queue']);

        // Stok tidak boleh berkurang
        $this->assertEquals($initialStock, $paracetamol->fresh()->stock);

        // Queue tidak boleh berstatus completed
        $this->assertEquals('in_progress', $queue->fresh()->status);
    }

    public function test_patient_can_view_their_electronic_medical_records_and_prescriptions(): void
    {
        $student = User::where('role', User::ROLE_STUDENT)->first();
        $poliUmum = Poli::where('slug', 'poliumum')->first();
        $paracetamol = Medicine::where('code', 'OBT-001')->first();

        $record = MedicalRecord::create([
            'record_code' => 'RM-202611-0001',
            'user_id' => $student->id,
            'poli_id' => $poliUmum->id,
            'doctor_name' => $poliUmum->doctor_name,
            'diagnosis' => 'Sindrom Dispepsia Akut',
            'symptoms' => 'Mual dan nyeri epigastrium',
            'blood_pressure' => '115/75',
            'temperature' => '36.8',
            'weight' => '58',
            'treatment_notes' => 'Pola makan teratur, hindari makanan pedas dan asam.',
        ]);

        PrescriptionItem::create([
            'medical_record_id' => $record->id,
            'medicine_id' => $paracetamol->id,
            'quantity' => 10,
            'dosage' => '3x1 tablet sesudah makan bila nyeri',
        ]);

        $response = $this->actingAs($student)->get('/KlickCare');

        $response->assertStatus(200);
        $response->assertSee('RM-202611-0001');
        $response->assertSee('Sindrom Dispepsia Akut');
        $response->assertSee('115/75 mmHg');
        $response->assertSee('36.8');
        $response->assertSee('Paracetamol 500mg');
        $response->assertSee('3x1 tablet sesudah makan bila nyeri');
    }
}
