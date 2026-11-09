<?php

namespace Database\Seeders;

use App\Models\Poli;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Database\Seeder;

class PoliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $polies = [
            [
                'name' => 'Poli Umum',
                'slug' => 'poliumum',
                'code_prefix' => 'U',
                'doctor_name' => 'dr. H. Hendra Setiawan, Sp.PD',
                'room' => 'Gedung Graha Polinema Lt. 1 - Ruang 101',
                'open_time' => '08:00',
                'close_time' => '15:30',
                'daily_quota' => 40,
                'icon_path' => 'img/icon/Poli Umum.png',
                'description' => 'Pelayanan pemeriksaan diagnosis awal, konsultasi medis, penanganan keluhan umum mahasiswa & dosen, serta rujukan poliklinik spesialis.',
                'is_active' => true,
            ],
            [
                'name' => 'Poli Gigi',
                'slug' => 'poligigi',
                'code_prefix' => 'G',
                'doctor_name' => 'drg. Maya Anindita',
                'room' => 'Gedung Graha Polinema Lt. 1 - Ruang 102',
                'open_time' => '08:30',
                'close_time' => '15:00',
                'daily_quota' => 25,
                'icon_path' => 'img/icon/Poli Gigi.png',
                'description' => 'Perawatan kesehatan gigi & mulut, scaling karang gigi, penambalan, pencabutan gigi sulung/permanen, dan edukasi kebersihan rongga mulut.',
                'is_active' => true,
            ],
            [
                'name' => 'Poli Mata',
                'slug' => 'polimata',
                'code_prefix' => 'M',
                'doctor_name' => 'dr. Farhan Malik, Sp.M',
                'room' => 'Gedung Graha Polinema Lt. 1 - Ruang 103',
                'open_time' => '09:00',
                'close_time' => '14:30',
                'daily_quota' => 20,
                'icon_path' => 'img/icon/Poli Mata.png',
                'description' => 'Pemeriksaan refraksi mata, tes buta warna (Ishihara), penanganan iritasi konjungtivitis, dan skrining Computer Vision Syndrome (CVS).',
                'is_active' => true,
            ],
            [
                'name' => 'Poli Gizi',
                'slug' => 'poligizi',
                'code_prefix' => 'Z',
                'doctor_name' => 'Nur Aini, S.Gz., RD',
                'room' => 'Gedung Graha Polinema Lt. 1 - Ruang 104',
                'open_time' => '08:00',
                'close_time' => '15:00',
                'daily_quota' => 25,
                'icon_path' => 'img/icon/Poli Gizi.png',
                'description' => 'Konsultasi gizi klinis, perencanaan pola makan sehat untuk mahasiswa, manajemen berat badan, dan pengukuran komposisi tubuh (BIA).',
                'is_active' => true,
            ],
        ];

        foreach ($polies as $data) {
            Poli::updateOrCreate(['slug' => $data['slug']], $data);
        }

        // Buat sample antrian hari ini jika ada mahasiswa
        $student = User::where('role', User::ROLE_STUDENT)->first();
        $staff = User::where('role', User::ROLE_STAFF)->first();
        $poliUmum = Poli::where('slug', 'poliumum')->first();
        $poliGigi = Poli::where('slug', 'poligigi')->first();

        if ($student && $poliUmum) {
            Queue::firstOrCreate(
                [
                    'user_id' => $student->id,
                    'visit_date' => today(),
                    'poli_id' => $poliUmum->id,
                ],
                [
                    'queue_code' => 'U-001',
                    'queue_number' => 1,
                    'complaint' => 'Demam dan pusing sejak 2 hari yang lalu',
                    'status' => 'in_progress',
                ]
            );
        }

        if ($staff && $poliGigi) {
            Queue::firstOrCreate(
                [
                    'user_id' => $staff->id,
                    'visit_date' => today(),
                    'poli_id' => $poliGigi->id,
                ],
                [
                    'queue_code' => 'G-001',
                    'queue_number' => 1,
                    'complaint' => 'Pemeriksaan rutin dan pembersihan karang gigi',
                    'status' => 'waiting',
                ]
            );
        }
    }
}
