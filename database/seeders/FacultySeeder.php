<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    private $faculties = [
        'FTE' => 'Fakultas Teknik Elektro',
        'FRI' => 'Fakultas Rekayasa Industri',
        'FIF' => 'Fakultas Informatika',
        'FEB' => 'Fakultas Ekonomi & Bisnis',
        'FKS' => 'Fakultas Komunikasi & Ilmu Sosial',
        'FIK' => 'Fakultas Industri Kreatif',
        'FIT' => 'Fakultas Ilmu Terapan'
    ];


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->faculties as $key => $value) {
            Faculty::query()->create([
                'nama_fakultas' => $value,
                'kode_fakultas' => $key,
            ]);
        }
    }
}
