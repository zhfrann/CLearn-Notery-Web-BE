<?php

namespace Database\Seeders\FIT\DAC;

use App\Models\Course;
use App\Models\Major;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class DACCourseSeeder extends Seeder
{
    private $DAC_semester1 = [
        'Algoritma Dan Pemrograman',
        'Bahasa Indonesia',
        'Dasar Komputer Dan Jaringan',
        'Internalisasi Budaya Dan Pembentukan Karakter',
        'Logika Matematika',
        'Pancasila',
        'Pengantar Manajemen Dan Bisnis',
        'Prinsip Akuntansi I',
    ];
    private $DAC_semester2 = [
        'Bahasa Inggris',
        'Basis Data Relasional',
        'Desain Antarmuka Pengguna',
        'Kewarganegaraan',
        'Literasi Teknologi',
        'Agama Budha',
        'Agama Hindu',
        'Agama Islam',
        'Agama Katolik',
        'Agama Kristen',
        'Agama Kong Hu Cu',
        'Penghayat Kepercayaan Terhadap Tuhan Yang Maha Esa',
        'Prinsip Akuntansi II',
        'Sistem Informasi Manajemen'
    ];
    private $DAC_semester3 = [
        'Akuntansi Biaya',
        'Akuntansi Keuangan Menengah',
        'Analisis Dan Perancangan Sistem Informasi',
        'Bahasa Inggris II',
        'Bahasa Query Terstruktur',
        'Dasar Perpajakan',
        'Pemrograman Web',
    ];
    private $DAC_semester4 = [
        'Akuntansi Manajemen',
        'Etika Profesi',
        'Kewirausahaan',
        'Manajemen Keuangan',
        'Pemrograman Web Berbasis Framework',
        'Pengembangan Profesionalisme',
        'Perencanaan Sumber Daya Perusahaan',
        'Perpajakan Lanjut'
    ];
    private $DAC_semester5 = [
        'Aplikasi Sistem Informasi Akuntansi',
        'Olahraga',
        'Pengujian Perangkat Lunak',
        'Perencanaan Sumber Daya Keuangan Perusahaan',
        'Proyek Terapan',
        'Statistika'
    ];
    private $DAC_semester6 = [
        'Magang',
        'Tugas Akhir',
        'Seminar Magang'
    ];

    public function run(): void
    {
        $major_id = Major::query()->where('kode_jurusan', 'DAC')->first()->major_id;
        $semester_ids = [];
        for ($i = 1; $i <= 6; $i++) {
            $semester_ids[$i] = Semester::query()->where('major_id', $major_id)->where('nomor_semester', $i)->first()->semester_id;
        }

        foreach ($this->DAC_semester1 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[1],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DAC_semester2 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[2],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DAC_semester3 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[3],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DAC_semester4 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[4],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DAC_semester5 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[5],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DAC_semester6 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[6],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
    }
}
