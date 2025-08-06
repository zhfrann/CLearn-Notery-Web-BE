<?php

namespace Database\Seeders\FIT\DMM;

use App\Models\Course;
use App\Models\Major;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class DMMCourseSeeder extends Seeder
{
    private $DMM_semester1 = [
        'Pengantar Pemasaran',
        'Manajemen',
        'Pengantar Ilmu Ekonomi',
        'Kewarganegaraan',
        'Agama Islam',
        'Agama Kristen',
        'Agama Katolik',
        'Agama Hindu',
        'Agama Budha',
        'Agama Kong Hu Cu',
        'Penghayat Kepercayaan Terhadap Tuhan yang Maha Esa',
        'Praktikum Aplikasi Perkantoran',
        'Internalisasi Budaya dan Pembentukan Karakter',
        'Statistika Pemasaran'
    ];
    private $DMM_semester2 = [
        'Manajemen Pemasaran',
        'Akuntansi dan Keuangan untuk Bisnis',
        'Pancasila',
        'Perilaku Konsumen Digital',
        'Pemasaran Jasa',
        'Bahasa Indonesia',
        'Bahasa Inggris',
        'Olah Raga',
    ];
    private $DMM_semester3 = [
        'Kewirausahaan',
        'Salesmanship',
        'Konten Pemasaran',
        'Pemasaran Ritel',
        'Pemasaran Digital',
        'Budgeting for Marketing',
        'Manajemen Produk dan Inovasi',
        'M.I.C.E Marketing',
    ];
    private $DMM_semester4 = [
        'Pemasaran Berbasis Komunitas',
        'Pengembangan Profesionalisme',
        'Periklanan dan Promosi Penjualan',
        'Manajemen Layanan Pelanggan',
        'Proyek Terapan',
        'Pemasaran Melalui Media Sosial',
        'Big Data Untuk Pemasaran'
    ];
    private $DMM_semester5 = [
        'Riset Pemasaran',
        'Perilaku Organisasi Bisnis',
        'Komunikasi Pemasaran',
        'Manajemen Hubungan Pelanggan',
        'Perencanaan Pemasaran',
        'Manajemen Human Capital',
        'Business English Presentation',
    ];
    private $DMM_semester6 = [
        'Magang',
        'Seminar Magang',
        'Tugas Akhir'
    ];

    public function run(): void
    {
        $major_id = Major::query()->where('kode_jurusan', 'DMM')->first()->major_id;
        $semester_ids = [];
        for ($i = 1; $i <= 6; $i++) {
            $semester_ids[$i] = Semester::query()->where('major_id', $major_id)->where('nomor_semester', $i)->first()->semester_id;
        }

        foreach ($this->DMM_semester1 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[1],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DMM_semester2 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[2],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DMM_semester3 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[3],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DMM_semester4 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[4],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DMM_semester5 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[5],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DMM_semester6 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[6],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
    }
}
