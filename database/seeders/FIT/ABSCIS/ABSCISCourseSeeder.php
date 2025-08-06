<?php

namespace Database\Seeders\FIT\ABSCIS;

use App\Models\Course;
use App\Models\Major;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class ABSCISCourseSeeder extends Seeder
{
    // Tambahkan array matkul per semester di sini
    private $ABSCIS_semester1 = [
        'Algoritma dan Pemrograman',
        'Desain Antarmuka Pengguna',
        'Pengantar Teknologi Informasi',
        'Sistem Informasi Manajemen Kota Cerdas',
        'Matematika Diskrit',
        'Bahasa Inggris',
        'Internalisasi Budaya dan Pembentukan Karakter',
        'Jaringan Komputer',
    ];
    private $ABSCIS_semester2 = [
        'Manajemen Proyek',
        'User Experience',
        'Pemrograman Web',
        'Basis Data I',
        'Pemodelan Proses Bisnis',
        'Bahasa Indonesia',
        'Pemrograman Berorientasi Objek',
    ];
    private $ABSCIS_semester3 = [
        'Analisis dan Perancangan Sistem Informasi',
        'Pengembangan Aplikasi Berbasis Web',
        'Dasar Pemrograman Perangkat Bergerak',
        'Analitik Data',
        'Kewirausahaan',
        'Statistika Terapan',
        'Agama Islam',
        'Agama Kristen',
        'Agama Katolik',
        'Agama Hindu',
        'Agama Budha',
        'Agama Kong Hu Cu',
        'Penghayat Kepercayaan Terhadap Tuhan Yang Maha Esa'
    ];
    private $ABSCIS_semester4 = [
        'Sistem dan Tata Kelola Kota Cerdas',
        'Pancasila',
        'Dasar Ilmu Data',
        'Proyek Inovasi Sistem Informasi Kota',
        'Pengujian Perangkat Lunak',
        'Basis Data II',
        'Visualisasi Data',
    ];
    private $ABSCIS_semester5 = [
        'Perilaku Organisasi',
        'Sumber Daya Teknologi Informasi Kota Cerdas',
        'Pengembangan Profesional',
        'Proyek Inovasi Sistem Informasi Kota Cerdas',
        'Ilmu Data Lanjut',
        'Kewarganegaraan',
        'Pemrograman Perangkat Bergerak Lanjut',
        'Etika Profesi',
    ];
    private $ABSCIS_semester6 = [
        'Manajemen Layanan SPBE',
        'Sistem Informasi Geografis',
        'Computer Vision',
        'Internet of Things',
        'Proyek Terapan',
        'Integrasi Data',
        'Manajemen Data',
    ];
    private $ABSCIS_semester7 = [
        'Seminar Magang',
        'Magang',
        'Bahasa Inggris Lanjut',
        'Olahraga',
    ];
    private $ABSCIS_semester8 = [
        'Tugas Akhir',
        'Kapita Selekta',
        'Keterampilan Presentasi Berbahasa Inggris',
    ];

    public function run(): void
    {
        $major_id = Major::query()->where('kode_jurusan', 'ABSCIS')->first()->major_id;
        $semester_ids = [];
        for ($i = 1; $i <= 8; $i++) {
            $semester_ids[$i] = Semester::query()->where('major_id', $major_id)->where('nomor_semester', $i)->first()->semester_id;
        }

        foreach ($this->ABSCIS_semester1 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[1],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        // Lakukan hal yang sama untuk semester 2-8
        foreach ($this->ABSCIS_semester2 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[2],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->ABSCIS_semester3 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[3],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->ABSCIS_semester4 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[4],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->ABSCIS_semester5 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[5],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->ABSCIS_semester6 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[6],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->ABSCIS_semester7 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[7],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->ABSCIS_semester8 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[8],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
    }
}
