<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    private $tags = [
        'Administrasi Bisnis',
        'Ekonomi',
        'Komunikasi',
        'Matematika',
        'Bahasa',
        'Teknologi Informasi',
        'Teknik',
        'Kesehatan',
        'Seni',
        'Pendidikan',
        'Hukum',
        'Psikologi',
        'Manajemen',
        'Akuntansi',
        'Statistika',
        'Fisika',
        'Kimia',
        'Biologi',
        'Geografi',
        'Sejarah',
        'Sosiologi',
        'Antropologi',
        'Filsafat',
        'Agama',
        'Olahraga',
        'Pariwisata',
        'Pertanian',
        'Peternakan',
        'Perikanan',
        'Kedokteran',
        'Farmasi',
        'Bisnis Digital',
        'Sistem Pakar',
        'Energi Terbarukan',
        'Kewirausahaan Sosial',
        'Teknologi Nano',
        'Analisis Data',
        'Kecerdasan Buatan',
        'Manajemen Risiko',
        'Pengolahan Citra',
        'Jaringan Komputer',
        'Pengembangan Web',
        'Keamanan Siber',
        'Rekayasa Perangkat',
        'Teknologi Blockchain',
        'Ekonomi Kreatif',
        'Statistika Industri',
        'Pemasaran Digital',
        'Desain Grafis',
        'Manajemen Proyek',
        'Sistem Operasi',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->tags as $value) {
            Tag::query()->create([
                'nama_tag' => $value
            ]);
        }
    }
}
