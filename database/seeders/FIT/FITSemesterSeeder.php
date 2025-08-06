<?php

namespace Database\Seeders\FIT;

use App\Models\Major;
use App\Models\Semester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FITSemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // foreach ($this->majorsFIT as $key => $value) {
        //     Major::query()->create([
        //         'faculty_id' => Faculty::query()->where('kode_fakultas', 'FIT')->get()->id,
        //         'nama_jurusan' => $value,
        //         'kode_jurusan' => $key
        //     ]);
        // }

        // DIF
        $DIF_major_id = Major::query()->where('kode_jurusan', 'DIF')->first()->major_id;
        for ($i = 1; $i <= 6; $i++) {
            Semester::query()->create([
                'major_id' => $DIF_major_id,
                'nomor_semester' => $i,
                'tahun_ajaran' => 2025
            ]);
        }

        // DIM
        $DIM_major_id = Major::query()->where('kode_jurusan', 'DIM')->first()->major_id;
        for ($i = 1; $i <= 6; $i++) {
            Semester::query()->create([
                'major_id' => $DIM_major_id,
                'nomor_semester' => $i,
                'tahun_ajaran' => 2025
            ]);
        }

        // DAC
        $DAC_major_id = Major::query()->where('kode_jurusan', 'DAC')->first()->major_id;
        for ($i = 1; $i <= 6; $i++) {
            Semester::query()->create([
                'major_id' => $DAC_major_id,
                'nomor_semester' => $i,
                'tahun_ajaran' => 2025
            ]);
        }

        // DCE
        $DCE_major_id = Major::query()->where('kode_jurusan', 'DCE')->first()->major_id;
        for ($i = 1; $i <= 6; $i++) {
            Semester::query()->create([
                'major_id' => $DCE_major_id,
                'nomor_semester' => $i,
                'tahun_ajaran' => 2025
            ]);
        }

        // DMM
        $DMM_major_id = Major::query()->where('kode_jurusan', 'DMM')->first()->major_id;
        for ($i = 1; $i <= 6; $i++) {
            Semester::query()->create([
                'major_id' => $DMM_major_id,
                'nomor_semester' => $i,
                'tahun_ajaran' => 2025
            ]);
        }

        // DHO
        $DHO_major_id = Major::query()->where('kode_jurusan', 'DHO')->first()->major_id;
        for ($i = 1; $i <= 6; $i++) {
            Semester::query()->create([
                'major_id' => $DHO_major_id,
                'nomor_semester' => $i,
                'tahun_ajaran' => 2025
            ]);
        }

        // DSM
        $DSM_major_id = Major::query()->where('kode_jurusan', 'DSM')->first()->major_id;
        for ($i = 1; $i <= 8; $i++) {
            Semester::query()->create([
                'major_id' => $DSM_major_id,
                'nomor_semester' => $i,
                'tahun_ajaran' => 2025
            ]);
        }

        // ABSCIS
        $ABSCIS_major_id = Major::query()->where('kode_jurusan', 'ABSCIS')->first()->major_id;
        for ($i = 1; $i <= 8; $i++) {
            Semester::query()->create([
                'major_id' => $ABSCIS_major_id,
                'nomor_semester' => $i,
                'tahun_ajaran' => 2025
            ]);
        }
    }
}
