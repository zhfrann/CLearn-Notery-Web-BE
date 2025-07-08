<?php

namespace Database\Seeders\FIT;

use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FITMajorSeeder extends Seeder
{
    private $majorsFIT = [
        'DTE' => 'D3 Teknik Telekomunikasi',
        'DIF' => 'D3 Rekayasa Perangkat Lunak Aplikasi',
        'DIM' => 'D3 Sistem Informasi',
        'DAC' => 'D3 Sistem Informasi Akuntansi',
        'DCE' => 'D3 Teknologi Komputer',
        'DMM' => 'D3 Manajemen Pemasaran',
        'DHO' => 'D3 Hospitality & Culinary Art (Perhotelan)',
        'DSM' => 'S1 Terapan Teknologi Rekayasa Multimedia',
        'ABSCIS' => 'S1 Terapan Sistem Informasi Kota Cerdas',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->majorsFIT as $key => $value) {
            Major::query()->create([
                'faculty_id' => Faculty::query()->where('kode_fakultas', 'FIT')->first()->faculty_id,
                'nama_jurusan' => $value,
                'kode_jurusan' => $key
            ]);
        }
    }
}
