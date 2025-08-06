<?php

namespace Database\Seeders\FEB;

use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FEBMajorSeeder extends Seeder
{
    private $majorsFEB = [
        'BMS' => 'S1 Manajemen Bisnis Telekomunikasi dan Informatika',
        'BAS' => 'S1 Akuntansi',
        'BLM' => 'S1 Manajemen Bisnis Kreasi',
        'BBA' => 'S1 Administrasi Bisnis',
        'MM' => 'S2 Manajemen',
        'MMPJJ' => 'S2 PJJ Manajemen',
        'MACC' => 'S2 Akuntansi',
        'MBA' => 'S2 Administrasi Bisnis'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->majorsFEB as $key => $value) {
            Major::query()->create([
                'faculty_id' => Faculty::query()->where('kode_fakultas', 'FEB')->first()->faculty_id,
                'nama_jurusan' => $value,
                'kode_jurusan' => $key
            ]);
        }
    }
}
