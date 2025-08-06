<?php

namespace Database\Seeders\FIT\DSM;

use App\Models\Course;
use App\Models\Major;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class DSMCourseSeeder extends Seeder
{
    private $DSM_semester1 = [
        'Internalisasi Budaya dan Pembentukan Karakter',
        'Bahasa Indonesia',
        'Literasi Data',
        'Aljabar Linier Elementer',
        'Matematika Diskrit',
        'Konten Digital 2D',
        'Sistem dan Jaringan Komputer',
        'Aplikasi Perkantoran'
    ];
    private $DSM_semester2 = [
        'Bahasa Inggris',
        'Agama Islam',
        'Desain Pengalaman Pengguna',
        'Desain dan Teknologi Multimedia',
        'Algoritma dan Pemrograman',
        'Statistika',
        'Konten Digital 3D',
    ];
    private $DSM_semester3 = [
        'Pancasila',
        'Desain Antarmuka Pengguna',
        'Kecerdasan Buatan',
        'Teknik Penceritaan Digital',
        'Pemrograman Berorientasi Objek',
        'Basis Data',
        'Pemrograman Web Interaktif 1',
    ];
    private $DSM_semester4 = [
        'Kewarganegaraan',
        'Kewirausahaan',
        'Pemrograman Simulator',
        'Rekayasa Aplikasi Multimedia',
        'Pemrograman Multimedia Interaktif',
        'Pengantar Bisnis TIK',
        'Proyek Multimedia 1',
    ];
    private $DSM_semester5 = [
        'Augmentasi Realitas',
        'Pemrograman Perangkat Bergerak Multimedia',
        'Pemrograman Web Interaktif 2',
        'Teknik Produksi Audio',
        'Gim Novel Visual',
        'Inovasi Bisnis TIK',
    ];
    private $DSM_semester6 = [
        'Pemrograman Gim',
        'Pengujian Aplikasi Multimedia',
        'Jaringan Pelanggan Bisnis TIK',
        'Teknik Produksi Video',
        'Virtualisasi Realitas',
        'Proyek Multimedia 2'
    ];
    private $DSM_semester7 = [
        'Kapita Selekta',
        'Seminar Proposal',
        'Seminar Magang',
        'Magang'
    ];
    private $DSM_semester8 = [
        'Olah Raga',
        'Manajemen Proyek',
        'Tugas Akhir',
        'Pengembangan Profesionalisme',
        'Bahasa Inggris : Professional',
        'Proyek Terapan'
    ];

    public function run(): void
    {
        $major_id = Major::query()->where('kode_jurusan', 'DSM')->first()->major_id;
        $semester_ids = [];
        for ($i = 1; $i <= 8; $i++) {
            $semester_ids[$i] = Semester::query()->where('major_id', $major_id)->where('nomor_semester', $i)->first()->semester_id;
        }

        foreach ($this->DSM_semester1 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[1],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DSM_semester2 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[2],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DSM_semester3 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[3],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DSM_semester4 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[4],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DSM_semester5 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[5],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DSM_semester6 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[6],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DSM_semester7 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[7],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DSM_semester8 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[8],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
    }
}
