<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            [
                'code' => 'OBT-001',
                'name' => 'Paracetamol 500mg',
                'category' => 'Analgesik & Antipiretik',
                'unit' => 'Tablet',
                'stock' => 150,
                'min_stock' => 30,
                'description' => 'Meredakan sakit kepala, sakit gigi, dan menurunkan demam.',
                'is_active' => true,
            ],
            [
                'code' => 'OBT-002',
                'name' => 'Amoxicillin 500mg',
                'category' => 'Antibiotik',
                'unit' => 'Kapsul',
                'stock' => 80,
                'min_stock' => 20,
                'description' => 'Antibiotik spektrum luas untuk infeksi bakteri saluran pernapasan dan gigi.',
                'is_active' => true,
            ],
            [
                'code' => 'OBT-003',
                'name' => 'Asam Mefenamat 500mg',
                'category' => 'Anti-inflamasi (NSAID)',
                'unit' => 'Kaplet',
                'stock' => 60,
                'min_stock' => 15,
                'description' => 'Meredakan nyeri gigi hebat, nyeri otot, dan peradangan pasca tindakan.',
                'is_active' => true,
            ],
            [
                'code' => 'OBT-004',
                'name' => 'Antasida Doen',
                'category' => 'Antasida Lambung',
                'unit' => 'Tablet Kunyah',
                'stock' => 90,
                'min_stock' => 25,
                'description' => 'Menetralkan asam lambung, meredakan gejala maag dan gastritis mahasiswa.',
                'is_active' => true,
            ],
            [
                'code' => 'OBT-005',
                'name' => 'Cetirizine 10mg',
                'category' => 'Antihistamin Alergi',
                'unit' => 'Tablet',
                'stock' => 70,
                'min_stock' => 15,
                'description' => 'Meredakan rhinitis alergi, bersin-bersin, biduran, dan gatal kulit.',
                'is_active' => true,
            ],
            [
                'code' => 'OBT-006',
                'name' => 'Vitamin C 500mg',
                'category' => 'Vitamin & Suplemen',
                'unit' => 'Tablet',
                'stock' => 200,
                'min_stock' => 40,
                'description' => 'Memelihara daya tahan tubuh mahasiswa selama masa perkuliahan dan ujian.',
                'is_active' => true,
            ],
            [
                'code' => 'OBT-007',
                'name' => 'Oralit Garam Rehidrasi',
                'category' => 'Elektrolit & Rehidrasi',
                'unit' => 'Sachet',
                'stock' => 85,
                'min_stock' => 20,
                'description' => 'Mencegah dan mengatasi dehidrasi akibat diare akut atau muntah.',
                'is_active' => true,
            ],
            [
                'code' => 'OBT-008',
                'name' => 'Cendo Cenfresh Tetes Mata 5ml',
                'category' => 'Obat Tetes Mata',
                'unit' => 'Botol',
                'stock' => 25,
                'min_stock' => 10,
                'description' => 'Mengatasi mata kering dan lelah akibat radiasi layar komputer (CVS).',
                'is_active' => true,
            ],
            [
                'code' => 'OBT-009',
                'name' => 'Betadine Gargle Obat Kumur 100ml',
                'category' => 'Antiseptik Mulut & Gigi',
                'unit' => 'Botol',
                'stock' => 20,
                'min_stock' => 5,
                'description' => 'Antiseptik rongga mulut untuk sariawan, radang gusi, dan bau mulut.',
                'is_active' => true,
            ],
            [
                'code' => 'OBT-010',
                'name' => 'Promag Cair Sachet',
                'category' => 'Antasida Lambung',
                'unit' => 'Sachet',
                'stock' => 6, // Disengaja menipis (< min_stock 15) untuk menguji indikator stok
                'min_stock' => 15,
                'description' => 'Suspensi pereda maag cepat saji untuk pertolongan darurat nyeri lambung.',
                'is_active' => true,
            ],
        ];

        foreach ($medicines as $item) {
            Medicine::updateOrCreate(['code' => $item['code']], $item);
        }
    }
}
