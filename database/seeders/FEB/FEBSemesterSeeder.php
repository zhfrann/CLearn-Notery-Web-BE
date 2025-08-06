<?php

namespace Database\Seeders\FEB;

use App\Models\Major;
use App\Models\Semester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FEBSemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // foreach ($this->majorsFEB as $key => $value) {
        //     Major::query()->create([
        //         'faculty_id' => Faculty::query()->where('kode_fakultas', 'FEB')->get()->id,
        //         'nama_jurusan' => $value,
        //         'kode_jurusan' => $key
        //     ]);
        // }

        // BMS
        $BMS_major_id = Major::query()->where('kode_jurusan', 'BMS')->first()->major_id;
        for ($i = 1; $i <= 8; $i++) {
            Semester::query()->create([
                'major_id' => $BMS_major_id,
                'nomor_semester' => $i,
                'tahun_ajaran' => 2025
            ]);
        }

        // BAS
        $BAS_major_id = Major::query()->where('kode_jurusan', 'BAS')->first()->major_id;
        for ($i = 1; $i <= 8; $i++) {
            Semester::query()->create([
                'major_id' => $BAS_major_id,
                'nomor_semester' => $i,
                'tahun_ajaran' => 2025
            ]);
        }

        // BLM
        $BLM_major_id = Major::query()->where('kode_jurusan', 'BLM')->first()->major_id;
        for ($i = 1; $i <= 8; $i++) {
            Semester::query()->create([
                'major_id' => $BLM_major_id,
                'nomor_semester' => $i,
                'tahun_ajaran' => 2025
            ]);
        }

        // BBA
        $BBA_major_id = Major::query()->where('kode_jurusan', 'BBA')->first()->major_id;
        for ($i = 1; $i <= 8; $i++) {
            Semester::query()->create([
                'major_id' => $BBA_major_id,
                'nomor_semester' => $i,
                'tahun_ajaran' => 2025
            ]);
        }

        // MM
        // $MM_major_id = Major::query()->where('kode_jurusan', 'MM')->first()->major_id;
        // for ($i = 1; $i <= 6; $i++) {
        //     Semester::query()->create([
        //         'major_id' => $MM_major_id,
        //         'nomor_semester' => $i,
        //         'tahun_ajaran' => 2025
        //     ]);
        // }

        // MPJJ
        // $MPJJ_major_id = Major::query()->where('kode_jurusan', 'MPJJ')->first()->major_id;
        // for ($i = 1; $i <= 6; $i++) {
        //     Semester::query()->create([
        //         'major_id' => $MPJJ_major_id,
        //         'nomor_semester' => $i,
        //         'tahun_ajaran' => 2025
        //     ]);
        // }

        // MACC
        // $MACC_major_id = Major::query()->where('kode_jurusan', 'MACC')->first()->major_id;
        // for ($i = 1; $i <= 8; $i++) {
        //     Semester::query()->create([
        //         'major_id' => $MACC_major_id,
        //         'nomor_semester' => $i,
        //         'tahun_ajaran' => 2025
        //     ]);
        // }

        // MBA
        // $MBA_major_id = Major::query()->where('kode_jurusan', 'MBA')->first()->major_id;
        // for ($i = 1; $i <= 8; $i++) {
        //     Semester::query()->create([
        //         'major_id' => $MBA_major_id,
        //         'nomor_semester' => $i,
        //         'tahun_ajaran' => 2025
        //     ]);
        // }
    }
}
