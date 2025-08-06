<?php

namespace Database\Seeders\FEB\BLM;

use App\Models\Course;
use App\Models\Major;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class BLMCourseSeeder extends Seeder
{
    private $BLM_semester1 = [
        'Agama Islam',
        'Agama Kristen',
        'Agama Katolik',
        'Agama Hindu',
        'Agama Buddha',
        'Agama Kong Hu Cu',
        'Akuntansi',
        'Bahasa Inggris',
        'Bahasa Mandarin 1',
        'Ekonomi Leisure',
        'Pengantar Bisnis Leisure',
        'Pengantar Manajemen Leisure',
        'Pembentukan Karakter Dalam Leisure'
    ];
    private $BLM_semester2 = [
        'Bahasa Indonesia',
        'Bahasa Inggris Lanjutan',
        'Bahasa Mandarin 2',
        'Bisnis Digital Untuk Leisure',
        'Pancasila',
        'Statistika Bisnis',
        'Teori Organisasi'
    ];
    private $BLM_semester3 = [
        'Bahasa Mandarin 3',
        'Bahasa Inggris Leisure',
        'Berpikir Kreatif Dalam Bisnis Leisure',
        'Manajemen Pemasaran Leisure',
        'Manajemen Jasa',
        'Manajemen Keuangan Leisure',
        'Perilaku Organisasi'
    ];
    private $BLM_semester4 = [
        'Kewirausahaan',
        'Manajemen Event',
        'Manajemen Jasa Pariwisata',
        'Manajemen Kualitas Pelayanan',
        'Manajemen Sdm Dan Kepemimpinan',
        'Manajemen Operasi Leisure',
        'Perilaku Konsumen Dalam Leisure'
    ];
    private $BLM_semester5 = [
        'Design Thinking Dan Inovasi',
        'Kewarganegaraan',
        'Komunikasi Digital',
        'Manajemen Proyek Leisure',
        'Penjenamaan Merek Destinasi',
        'Perencanaan Bisnis Leisure',
        'Sistem Informasi Manajemen Leisure'
    ];
    private $BLM_semester6 = [
        'Big Data Untuk Industri Leisure',
        'Manajemen Strategik Untuk Leisure',
        'Manajemen Risiko',
        'Manajemen Pendapatan Leisure',
        'Metodologi Penelitian Untuk Leisure',
        'Pariwisata Berkelanjutan Dan Ekowisata'
    ];
    private $BLM_semester7 = [
        'Etika Dan Regulasi Untuk Leisure',
        'Manajemen Destinasi Leisure',
        'Manajemen Fasilitas Dan Properti',
        'Manajemen Dan Desain Fasilitas',
        'Model Bisnis Acara Seni Dan Budaya',
        'Pengembangan Komunitas Berkelanjutan',
        'Pemasaran Pagelaran Seni Dan Budaya',
        'Proyek Penjenamaan Destinasi',
        'Manajemen Acara Game Dan E-sport',
        'Manajemen Acara Olah Raga',
        'Model Bisnis Acara Olahraga',
        'Model Bisnis Game Dan E-sport',
        'Pengelolaan Logistik Event',
        'Teknologi Game Dan E-sport'
    ];
    private $BLM_semester8 = [
        'Sertifikasi',
        'Tugas Akhir 1',
        'Tugas Akhir 2'
    ];

    public function run(): void
    {
        $major_id = Major::query()->where('kode_jurusan', 'BLM')->first()->major_id;
        $semester_ids = [];
        for ($i = 1; $i <= 8; $i++) {
            $semester_ids[$i] = Semester::query()->where('major_id', $major_id)->where('nomor_semester', $i)->first()->semester_id;
        }

        foreach ($this->BLM_semester1 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[1],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BLM_semester2 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[2],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BLM_semester3 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[3],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BLM_semester4 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[4],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BLM_semester5 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[5],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BLM_semester6 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[6],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BLM_semester7 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[7],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->BLM_semester8 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[8],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
    }
}
