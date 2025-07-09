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
        'Bahasa'
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
