<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\NoteFile;
use App\Models\NoteTag;
use App\Models\NoteStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellerIds = [1, 2, 3, 4];
        $courseIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30];
        $previewImages = [
            'notes/files/note1.png',
            'notes/files/note2.png',
            'notes/files/note3.png',
            'notes/files/note4.png',
        ];

        $sampleFiles = [
            ['nama_file' => 'Materi-Kalkulus.pdf', 'path_file' => 'notes/files/note3.png'],
            ['nama_file' => 'Modul-Jaringan.docx', 'path_file' => 'notes/files/note4.png'],
            ['nama_file' => 'Struktur-Data.pdf', 'path_file' => 'notes/files/note1.png'],
            ['nama_file' => 'Algoritma-Coding.png', 'path_file' => 'notes/files/note2.png'],
        ];

        for ($i = 1; $i <= 20; $i++) {
            $seller = collect($sellerIds)->random();
            $course = collect($courseIds)->random();
            $judul = "Note sample ke-$i";
            $deskripsi = fake()->paragraph(3);
            $harga = rand(5000, 25000);
            $preview = collect($previewImages)->random();

            $note = Note::create([
                'seller_id' => $seller,
                'course_id' => $course,
                'judul' => $judul,
                'deskripsi' => $deskripsi,
                'harga' => $harga,
                'jumlah_like' => rand(5, 100),
                'jumlah_dikunjungi' => rand(100, 1000),
                'gambar_preview' => $preview,
            ]);

            NoteStatus::create([
                'note_id' => $note->note_id,
                'status' => 'diterima',
            ]);

            // Masukkan file acak (1–3 file)
            $filesToInsert = collect($sampleFiles)->shuffle()->take(rand(1, 3))->map(function ($file) use ($note) {
                return [
                    'note_id' => $note->note_id,
                    'nama_file' => $file['nama_file'],
                    'path_file' => $file['path_file'],
                    'tipe' => pathinfo($file['path_file'], PATHINFO_EXTENSION),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            NoteFile::insert($filesToInsert->toArray());

            // Masukkan tag acak (asumsikan tag_id 1–5)
            $tagIds = collect(range(1, 4))->shuffle()->take(rand(1, 3));
            NoteTag::insert($tagIds->map(fn($tagId) => [
                'note_id' => $note->note_id,
                'tag_id' => $tagId,
            ])->toArray());
        }
    }
}
