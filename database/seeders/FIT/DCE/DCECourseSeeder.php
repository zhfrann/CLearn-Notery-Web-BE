<?php

namespace Database\Seeders\FIT\DCE;

use App\Models\Course;
use App\Models\Major;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class DCECourseSeeder extends Seeder
{
    private $DCE_semester1 = [
        'Sistem Komputer',
        'Elektronika Dasar',
        'Matematika Teknik I',
        'Agama Islam',
        'Agama Kristen',
        'Agama Katolik',
        'Agama Hindu',
        'Agama Budha',
        'Agama Kong Hu Cu',
        'Rangkaian Elektrik',
        'Algoritma dan Pemrograman',
        'Internalisasi Budaya & Pembentukan Karakter',
        'Pancasila',
    ];
    private $DCE_semester2 = [
        'Sistem Digital',
        'Basis Data',
        'Mikroelektronika',
        'Sistem Jaringan Komputer',
        'Algoritma dan Pemrograman Lanjut',
        'Bahasa Inggris',
        'Matematika Teknik II',
    ];
    private $DCE_semester3 = [
        'Kewarganegaraan',
        'Pemrograman Web',
        'Routing dan Switching',
        'Interface, Peripheral, dan Komunikasi',
        'Layanan Jaringan',
        'Sistem Mikrokontroler',
        'Bahasa Inggris 2',
        'Probabilitas dan Statistika',
    ];
    private $DCE_semester4 = [
        'Administrasi Jaringan',
        'Bahasa Indonesia',
        'Keamanan Jaringan',
        'Jaringan Lanjut',
        'Sistem Kendali',
        'Sistem PLC',
        'Pengolahan Sinyal Digital'
    ];
    private $DCE_semester5 = [
        'Internet of Things',
        'Pengembangan Profesional',
        'Teknik Presentasi dan Pelaporan',
        'Sistem Tertanam',
        'Olahraga',
        'Kewirausahaan',
        'Proyek Terapan',
    ];
    private $DCE_semester6 = [
        'Magang',
        'Seminar Magang',
        'Proyek Akhir'
    ];

    public function run(): void
    {
        $major_id = Major::query()->where('kode_jurusan', 'DCE')->first()->major_id;
        $semester_ids = [];
        for ($i = 1; $i <= 6; $i++) {
            $semester_ids[$i] = Semester::query()->where('major_id', $major_id)->where('nomor_semester', $i)->first()->semester_id;
        }

        foreach ($this->DCE_semester1 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[1],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DCE_semester2 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[2],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DCE_semester3 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[3],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DCE_semester4 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[4],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DCE_semester5 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[5],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DCE_semester6 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[6],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
    }
}
