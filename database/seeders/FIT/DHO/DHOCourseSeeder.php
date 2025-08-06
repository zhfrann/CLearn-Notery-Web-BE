<?php

namespace Database\Seeders\FIT\DHO;

use App\Models\Course;
use App\Models\Major;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class DHOCourseSeeder extends Seeder
{
    private $DHO_semester1 = [
        'Pengantar Ilmu Pariwisata dan Hospitality',
        'Reservasi Hotel',
        'Operasional Penyiapan Public Area Hotel',
        'Operasional Restoran I',
        'Teknik Pengolahan Makanan Kontinental',
        'Etika profesi',
        'Pengantar Manajemen',
        'Pancasila',
        'Pembentukan Karater HEI',
    ];
    private $DHO_semester2 = [
        'Registrasi Hotel',
        'Operasional Penyiapan Kamar Hotel',
        'Operasional Restoran II',
        'Teknik Pengolahan Makanan Oriental',
        'Teknik Pengolahan Roti Cepat dan Adonan Beragi',
        'Keamanan, Kebersihan dan Sanitasi',
        'Pendidikan Agama dan Etika',
        'Pengantar Bahasa Inggris',
    ];
    private $DHO_semester3 = [
        'Layanan Informasi Hotel',
        'Operasional Laundry dan Linen Hotel',
        'Operasional Banquet dan Room Service Hotel',
        'Teknik Pengolahan Masakan Tradisional',
        'Teknik Pengolahan Kue, Gateux dan Torte',
        'Bahasa Inggris Untuk Divisi Makanan dan Minuman',
        'Psikologi Pelayanan',
        'Bahasa Indonesia',
    ];
    private $DHO_semester4 = [
        'Manajemen Divisi Kamar',
        'Gastronomi dan Seni Kuliner',
        'Metodologi Penelitian Pariwisata',
        'Teknik Pengolahan Minuman',
        'Teknik Pengolahan Gula dan Coklat',
        'Bahasa Inggris Untuk Divisi Kamar',
        'Kewirausahaan',
        'Literasi Teknologi',
        'Olahraga'
    ];
    private $DHO_semester5 = [
        'Magang'
    ];
    private $DHO_semester6 = [
        'Proyek Akhir',
        'Manajemen Sumber Daya Manusia',
        'Sales Dan Marketing Pariwisata',
        'Pengendalian Biaya',
        'Bahasa Inggris Untuk Pariwisata',
        'Manajemen Perhelatan / MICE',
        'Bahasa Asing',
        'Kewarganegaraan',
        'Pengembangan Profesionalisme'
    ];

    public function run(): void
    {
        $major_id = Major::query()->where('kode_jurusan', 'DHO')->first()->major_id;
        $semester_ids = [];
        for ($i = 1; $i <= 6; $i++) {
            $semester_ids[$i] = Semester::query()->where('major_id', $major_id)->where('nomor_semester', $i)->first()->semester_id;
        }

        foreach ($this->DHO_semester1 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[1],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DHO_semester2 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[2],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DHO_semester3 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[3],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DHO_semester4 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[4],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DHO_semester5 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[5],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
        foreach ($this->DHO_semester6 as $value) {
            Course::query()->create([
                'semester_id' => $semester_ids[6],
                'major_id' => $major_id,
                'nama_mk' => $value
            ]);
        }
    }
}
