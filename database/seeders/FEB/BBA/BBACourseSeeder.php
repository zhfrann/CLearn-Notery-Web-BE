<?php

namespace Database\Seeders\FEB\BBA;

use App\Models\Course;
use App\Models\Major;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class BBACourseSeeder extends Seeder
{
    private $BBA_semester1 = [
        'Pembentukan Karakter',
        'Aplikasi Komputer Dan Pemrograman',
        'Pengantar Administrasi Dan Bisnis',
        'Komunikasi Bisnis',
        'Perilaku Organisasi',
        'Ekonomi Bisnis',
        'Design Thinking'
    ];
    private $BBA_semester2 = [
        'Statistik Bisnis',
        'Etika Dan Hukum Bisnis',
        'Akuntansi Keuangan Dasar',
        'Pemasaran',
        'Sumber Daya Manusia',
        'Kreativitas Dan Inovasi'
    ];
    private $BBA_semester3 = [
        'Keuangan',
        'Operasi Bisnis',
        'Strategi Dan Kebijakan Bisnis',
        'Tata Kelola Perusahaan',
        'Personal Branding',
        'Perpajakan',
        'Lembaga Dan Teknologi Keuangan'
    ];
    private $BBA_semester4 = [
        'Analisis Resiko',
        'Riset Operasi Bisnis',
        'Manajemen Proyek',
        'Business Intelligence Dan Analitik',
        'Kepemimpinan',
        'Sistem Informasi Bisnis',
        'Manajemen Pengetahuan'
    ];
    private $BBA_semester5 = [
        'Agama Islam',
        'Agama Kristen',
        'Agama Katolik',
        'Agama Hindu',
        'Agama Budha',
        'Agama Konghucu',
        'Kewarganegaraan',
        'Bahasa Inggris',
        'Literasi Teknologi',
        'Metodologi Penelitian Bisnis',
        'Model Bisnis',
        'Proses Bisnis',
        'Rantai Pasok Dalam Bisnis'
    ];
    private $BBA_semester6 = [
        'Pancasila',
        'Bahasa Indonesia',
        'Kewirausahaan Digital',
        'Riset Pemasaran',
        'Saluran Bisnis Dan Ritel',
        'Franchising',
        'Pemasaran Digital',
        'Manajemen Kualitas',
        'Pemasaran Jasa',
        'Manajemen Penjualan',
        'Manajemen Perubahan',
        'Enterprise Analysis',
        'Machine Learning Dalam Bisnis',
        'Wrap Entrepreneurship – Bisnis Startup',
        'Wrap Entrepreneurship – Pengembangan Bisnis',
        'Wrap Entrepreneurship – Peluncuran Bisnis Baru',
        'Wrap Researchship – Tinjauan Literatur',
        'Wrap Researchship – Desain Riset',
        'Wrap Researchship – Publikasi Jurnal Nasional Terindeks',
        'Wrap Internship – Proposal Magang',
        'Wrap Internship – Pengalaman Dalam Industri',
        'Wrap Internship – Praktik Profesional'
    ];
    private $BBA_semester7 = [
        'Kerja Praktik',
        'Proposal Tugas Akhir',
        'Entrepreneurial Branding',
        'Kewirausahaan Kreatif Dan Budaya',
        'Strategi Kewirausahaan',
        'Keuangan Kewirausahaan',
        'Penganggaran',
        'Pengembangan Sumber Daya Manusia',
        'Entreprise Resource Planning',
        'Analisis Konsumen',
        'Negosiasi Bisnis',
        'Wrap Researchship – Metodologi Evaluasi',
        'Wrap Researchship – Pengukuran Dan Pengujian Riset',
        'Wrap Researchship – Publikasi Jurnal Internasional',
        'Wrap Internship – Pengalaman Dalam Industri Lanjutan',
        'Wrap Internship – Praktik Profesional Lanjutan',
        'Wrap Internship – Laporan Akhir Magang',
        'Sertifikasi'
    ];
    private $BBA_semester8 = [
        'Tugas Akhir',
        'Pengusaha Sosial',
        'Bisnis Mikro Dan Kecil',
        'Kinerja Dan Remunerasi',
        'Bisnis Internasional',
        'Analisis Portofolio',
        'Perencanaan Keuangan'
    ];

    public function run(): void
    {
        $major_id = Major::query()->where('kode_jurusan', 'BBA')->first()->major_id;
        $semester_ids = [];
        for ($i = 1; $i <= 8; $i++) {
            $semester_ids[$i] = Semester::query()->where('major_id', $major_id)->where('nomor_semester', $i)->first()->semester_id;
        }

        foreach ($this->BBA_semester1 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[1],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BBA_semester2 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[2],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BBA_semester3 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[3],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BBA_semester4 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[4],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BBA_semester5 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[5],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BBA_semester6 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[6],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BBA_semester7 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[7],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BBA_semester8 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[8],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
    }
}
