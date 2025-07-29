<?php

namespace Database\Seeders;

use App\Models\ReportType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reportTypes = [
            [
                'value' => 'penyalahgunaan',
                'label' => 'Penyalahgunaan',
                'description' => 'Penyalahgunaan platform, spam, atau perilaku tidak pantas lainnya',
                'sort_order' => 1
            ],
            [
                'value' => 'penipuan_konten_tidak_sesuai',
                'label' => 'Penipuan/konten tidak sesuai',
                'description' => 'Konten yang dijual tidak sesuai dengan deskripsi atau mengandung penipuan',
                'sort_order' => 2
            ],
            [
                'value' => 'pemerasan',
                'label' => 'Pemerasan',
                'description' => 'Upaya pemerasan atau ancaman untuk keuntungan pribadi',
                'sort_order' => 3
            ],
            [
                'value' => 'pencemaran_nama_baik',
                'label' => 'Pencemaran nama baik',
                'description' => 'Fitnah, pencemaran nama baik, atau defamasi terhadap user lain',
                'sort_order' => 4
            ],
            [
                'value' => 'pelanggaran_hak_privasi',
                'label' => 'Pelanggaran hak dan privasi',
                'description' => 'Pelanggaran privasi, penyebaran informasi pribadi tanpa izin',
                'sort_order' => 5
            ],
            [
                'value' => 'pemalsuan',
                'label' => 'Pemalsuan',
                'description' => 'Pemalsuan identitas, dokumen, atau informasi akademik',
                'sort_order' => 6
            ],
            [
                'value' => 'penyebaran_konten_terlarang',
                'label' => 'Penyebaran konten terlarang',
                'description' => 'Konten yang melanggar hukum, norma, atau kebijakan platform',
                'sort_order' => 7
            ]
        ];

        foreach ($reportTypes as $type) {
            ReportType::create($type);
        }
    }
}
