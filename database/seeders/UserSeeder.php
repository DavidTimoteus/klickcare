<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Admin / Tenaga Medis
        User::updateOrCreate(
            ['email' => 'admin@admin.polinema.ac.id'],
            [
                'name' => 'dr. Dian Pratama (Admin Poliklinik)',
                'noId' => '198501012010121001',
                'password' => Hash::make('password123'),
                'gender' => 'Laki-Laki',
                'birthDate' => '1985-01-01',
                'address' => 'Jl. Soekarno Hatta No. 9, Malang',
                'phoneNumber' => '081234567890',
                'medical_record_number' => 'RM-MED-0001',
                'allergies' => 'Tidak ada riwayat alergi obat',
                'blood_type' => 'O',
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // 2. Akun Dosen / Tenaga Kependidikan
        User::updateOrCreate(
            ['email' => 'dosen@polinema.ac.id'],
            [
                'name' => 'Ir. Bambang Wijaya, M.T.',
                'noId' => '197505052002121002',
                'password' => Hash::make('password123'),
                'gender' => 'Laki-Laki',
                'birthDate' => '1975-05-05',
                'address' => 'Perumahan Dosen Polinema Blok B-12, Malang',
                'phoneNumber' => '081234567891',
                'medical_record_number' => 'RM-POL-0002',
                'allergies' => 'Tidak ada riwayat alergi obat',
                'blood_type' => 'A',
                'role' => User::ROLE_STAFF,
                'email_verified_at' => now(),
            ]
        );

        // 3. Akun Mahasiswa
        User::updateOrCreate(
            ['email' => '2131710012@student.polinema.ac.id'],
            [
                'name' => 'David Timoteus',
                'noId' => '2131710012',
                'password' => Hash::make('password123'),
                'gender' => 'Laki-Laki',
                'birthDate' => '2002-12-17',
                'address' => 'Jl. Kembang Turi No. 15, Lowokwaru, Malang',
                'phoneNumber' => '085712345678',
                'medical_record_number' => 'RM-POL-0003',
                'allergies' => 'Alergi Golongan Penisilin (Amoxicillin)',
                'blood_type' => 'B',
                'role' => User::ROLE_STUDENT,
                'email_verified_at' => now(),
            ]
        );
    }
}
