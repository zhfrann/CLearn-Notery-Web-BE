<?php

namespace Database\Seeders\FEB\BAS;

use App\Models\Course;
use App\Models\Major;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class BASCourseSeeder extends Seeder
{
    private $BAS_semester1 = [
        'Agama Islam',
        'Agama Kristen',
        'Agama Katolik',
        'Agama Hindu',
        'Agama Budha',
        'Agama Kong Hu Cu',
        'Penghayat Kepercayaan Terhadap Tuhan Yme',
        'Pengantar Akuntansi',
        'Perpajakan',
        'Sistem Informasi Manajemen',
        'Laboratorium Akuntansi',
        'Kewarganegaraan',
        'Pancasila'
    ];
    private $BAS_semester2 = [
        'Akuntansi Keuangan Menengah 1',
        'Akuntansi Biaya',
        'Implementasi Perpajakan',
        'Manajemen Keuangan Perusahaan',
        'Manajemen Resiko',
        'Akuntansi Publik'
    ];
    private $BAS_semester3 = [
        'Akuntansi Keuangan Menengah 2',
        'Sistem Informasi Akuntansi Dan Pengendalian Internal',
        'Audit',
        'Sistem Pengendalian Manajemen',
        'Aplikasi Analisis Akuntansi',
        'Akuntansi Manajemen',
        'Aplikasi Pelaporan Dan Presentasi'
    ];
    private $BAS_semester4 = [
        'Akuntansi Keuangan Lanjutan',
        'Implementasi Audit',
        'Analisis Laporan Keuangan',
        'Teori Akuntansi',
        'Etika Dan Tata Kelola Akuntansi',
        'Rekayasa Laporan Keuangan'
    ];
    private $BAS_semester5 = [
        'Literasi Manusia',
        'Kewirausahaan',
        'Ekonomi',
        'Manajemen Bisnis',
        'Literasi Teknologi',
        'Matematika Untuk Ekonomi Dan Bisnis',
        'Hukum Komersial Dan Perusahaan',
        'Bahasa Inggris'
    ];
    private $BAS_semester6 = [
        'Percakapan Dalam Bahasa Asing',
        'Manajemen Perpajakan',
        'Literasi Data',
        'Magang Dan Pengabdian Masyarakat',
        'Statistik Riset Bisnis',
        'Bahasa Indonesia',
        'Analitik Data Akuntansi'
    ];
    private $BAS_semester7 = [
        'Penulisan Akademik',
        'Aplikasi Riset Akuntansi',
        'Akuntansi Komprehensif',
        'Metodologi Penelitian Akuntansi Dan Keuangan',
        'Sertifikasi',
        'Akuntansi Kontemporer',
        'Investasi'
    ];
    private $BAS_semester8 = [
        'Audit Sistem Informasi',
        'Proposal',
        'Tugas Akhir',
        'Manajemen Strategik Digital',
        'Audit Investigasi Dan Akuntansi Forensik',
        'Manajemen Perpajakan',
        'Pelaporan Dan Jaminan Berkelanjutan'
    ];

    public function run(): void
    {
        $major_id = Major::query()->where('kode_jurusan', 'BAS')->first()->major_id;
        $semester_ids = [];
        for ($i = 1; $i <= 8; $i++) {
            $semester_ids[$i] = Semester::query()->where('major_id', $major_id)->where('nomor_semester', $i)->first()->semester_id;
        }

        foreach ($this->BAS_semester1 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[1],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BAS_semester2 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[2],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BAS_semester3 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[3],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BAS_semester4 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[4],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BAS_semester5 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[5],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BAS_semester6 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[6],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BAS_semester7 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[7],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BAS_semester8 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[8],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
    }
}
