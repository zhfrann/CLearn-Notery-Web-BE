<?php

namespace Database\Seeders\FIT\DIM;

use App\Models\Course;
use App\Models\Major;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class DIMCourseSeeder extends Seeder
{
    private $DIM_semester1 = [
        'Algoritma dan Pemrograman',
        'Desain Antarmuka Pengguna',
        'Pengantar Teknologi Informasi',
        'Sistem Informasi Manajemen',
        'Matematika Diskrit',
        'Bahasa Inggris',
        'Internalisasi Budaya dan Pembentukan Karakter',
        'Jaringan Komputer',
    ];
    private $DIM_semester2 = [
        'Manajemen Proyek',
        'User Experience',
        'Pemrograman Web',
        'Basis Data I',
        'Pemodelan Proses Bisnis',
        'Bahasa Indonesia',
        'Pemrograman Berorientasi Objek'
    ];
    private $DIM_semester3 = [
        'Analisis dan Perancangan Sistem Informasi',
        'Pengembangan Aplikasi Berbasis Web',
        'Dasar Pemrograman Perangkat Bergerak',
        'Basis Data II',
        'Kewirausahaan',
        'Statistika Terapan',
        'Etika Profesi',
    ];
    private $DIM_semester4 = [
        'Pengembangan Profesionalisme',
        'Pemrograman Perangkat Bergerak Lanjut',
        'Dasar Ilmu Data',
        'Proyek Sistem Informasi',
        'Pengujian Perangkat Lunak',
        'Bahasa Inggris Lanjut',
        'Visualisasi Data',
        'Agama Islam',
        'Agama Kristen',
        'Agama Katolik',
        'Agama Hindu',
        'Agama Buddha',
        'Agama Kong Hu Cu',
        'Penghayat Kepercayaan Terhadap Tuhan Yang Maha Esa'
    ];
    private $DIM_semester5 = [
        'Perilaku Organisasi',
        'Olah Raga',
        'Inovasi Teknologi',
        'Proyek Terapan',
        'Ilmu Data Lanjut',
        'Kewarganegaraan',
        'Pancasila'
    ];
    private $DIM_semester6 = [
        'Seminar Magang',
        'Magang',
        'Tugas Akhir'
    ];

    public function run(): void
    {
        $major_id = Major::query()->where('kode_jurusan', 'DIM')->first()->major_id;
        $semester_ids = [];
        for ($i = 1; $i <= 6; $i++) {
            $semester_ids[$i] = Semester::query()->where('major_id', $major_id)->where('nomor_semester', $i)->first()->semester_id;
        }

        foreach ($this->DIM_semester1 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[1],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DIM_semester2 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[2],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DIM_semester3 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[3],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DIM_semester4 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[4],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DIM_semester5 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[5],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DIM_semester6 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[6],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
    }
}
