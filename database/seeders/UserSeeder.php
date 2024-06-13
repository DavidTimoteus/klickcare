<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tbl_users')->insert([
            [
                'name' => 'Admin',
                'noId' => '2131710012',
                'email' => 'admin@admin.polinema.ac.id',
                'password' => Hash::make('password123'),
                'gender' => 'Laki-Laki',
                'birthDate' => '2002-12-17',
                'address' => 'Jl. Soekarno Hatta No.9',
                'phoneNumber' => '08123456789',
                'role' => 1,
                'create_id' => null,
                'creation_time' => Carbon::now(),
                'update_id' => null,
                'update_time' => Carbon::now(),
            ],
            [
                'name' => 'David',
                'noId' => '2141762012',
                'email' => '2131710012@student.polinema.ac.id',
                'password' => Hash::make('password123'),
                'gender' => 'Laki-Laki',
                'birthDate' => '2002-12-17',
                'address' => 'Jl. Soekarno Hatta No.9',
                'phoneNumber' => '08123456789',
                'role' => 2,
                'create_id' => null,
                'creation_time' => Carbon::now(),
                'update_id' => null,
                'update_time' => Carbon::now(),
            ],
        ]);
    }
}
