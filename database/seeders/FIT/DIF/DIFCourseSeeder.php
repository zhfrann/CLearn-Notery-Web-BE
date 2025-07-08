<?php

namespace Database\Seeders\FIT\DIF;

use App\Models\Course;
use App\Models\Major;
use App\Models\Semester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DIFCourseSeeder extends Seeder
{
    // DIF Semester 1
    private $DIF_semester1 = [
        'Implementasi Algoritma',
        'Dasar Sistem Komputer',
        'Matematika Informatika',
        'Alat Bantu Desain Antarmuka',
        'Internalisasi Budaya Dan Pembentukan Karakter',
        'Analisis Kebutuhan dan Perancangan Perangkat Lunak',
        'Agama Islam',
        'Agama Kristen',
        'Agama Khatolik',
        'Agama Hindu',
        'Agama Buddha',
        'Agama Kong Hu Cu',
        'Agama Penghayat Kepercayaan Terhadap Tuhan Yang Maha Esa',
        'Bahasa Indonesia',
    ];

    // DIF Semester 2
    private $DIF_semester2 = [
        'Implementasi Struktur Data',
        'Pemrograman Berbasis Web 1',
        'Matematika Informatika 2',
        'Instalasi Jaringan Komputer',
        'Bahasa Inggris',
        'Sistem Basis Data 1'
    ];

    // DIF Semester 3
    private $DIF_semester3 = [
        'Keamanan Perangkat Lunak',
        'Pemrograman Berbasis Web 2',
        'Perancangan Pengalaman Pengguna',
        'Pemrograman Berorientasi Obyek',
        'Sistem Basis Data 2',
        'Pengembangan Profesionalisme'
    ];

    // DIF Semester 4
    private $DIF_semester4 = [
        'Proyek Terapan',
        'Pemrograman untuk Perangkat Bergerak 1',
        'Pengembangan Aplikasi Gim',
        'Teknologi Imersif',
        'Penjaminan Kualitas Perangkat Lunak',
        'Kecerdasan Artifisial Terapan'
    ];

    // DIF Semester 5
    private $DIF_semester5 = [
        'Pemrograman untuk Perangkat Bergerak 2',
        'Implementasi Internet of Things',
        'Manajemen Proyek IT',
        'Pancasila',
        'Kewarganegaraan',
        'Olahraga',
        'Kewirausahaan'
    ];

    // DIF Semester 6
    private $DIF_semester6 = [
        'Tugas Akhir',
        'Magang',
        'Seminar Magang',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $major_id = Major::query()->where('kode_jurusan', 'DIF')->first()->major_id;
        $semester1_id = Semester::query()->where('major_id', $major_id)->where('nomor_semester', 1)->first()->semester_id;
        $semester2_id = Semester::query()->where('major_id', $major_id)->where('nomor_semester', 2)->first()->semester_id;
        $semester3_id = Semester::query()->where('major_id', $major_id)->where('nomor_semester', 3)->first()->semester_id;
        $semester4_id = Semester::query()->where('major_id', $major_id)->where('nomor_semester', 4)->first()->semester_id;
        $semester5_id = Semester::query()->where('major_id', $major_id)->where('nomor_semester', 5)->first()->semester_id;
        $semester6_id = Semester::query()->where('major_id', $major_id)->where('nomor_semester', 6)->first()->semester_id;

        foreach ($this->DIF_semester1 as $value) {
            Course::query()->create([
                'semester_id' => $semester1_id,
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }

        foreach ($this->DIF_semester2 as $value) {
            Course::query()->create([
                'semester_id' => $semester2_id,
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }

        foreach ($this->DIF_semester3 as $value) {
            Course::query()->create([
                'semester_id' => $semester3_id,
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }

        foreach ($this->DIF_semester4 as $value) {
            Course::query()->create([
                'semester_id' => $semester4_id,
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }

        foreach ($this->DIF_semester5 as $value) {
            Course::query()->create([
                'semester_id' => $semester5_id,
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }

        foreach ($this->DIF_semester6 as $value) {
            Course::query()->create([
                'semester_id' => $semester6_id,
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
    }
}
