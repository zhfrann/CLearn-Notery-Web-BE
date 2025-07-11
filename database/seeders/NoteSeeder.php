<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\NoteFile;
use App\Models\NoteTag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    private $notes = [
        [
            'seller_id' => 1,
            'course_id' => 1,
            'judul' => 'Note sample 1',
            'deskripsi' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis vulputate lacus in magna fringilla, at placerat neque tempor. Nam mollis in eros ut convallis. Integer ut rutrum lacus, eget auctor lorem. Vestibulum a tortor vitae.',
            'harga' => 20000,
            'gambar_preview' => 'notes/files/note1.png',
            'files' => [
                ['nama_file' => 'Ekonomi-Makro.pdf', 'path_file' => 'notes/files/note3.png'],
            ],
        ],
        [
            'seller_id' => 2,
            'course_id' => 2,
            'judul' => 'Note sample 2',
            'deskripsi' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis vulputate lacus in magna fringilla, at placerat neque tempor. Nam mollis in eros ut convallis. Integer ut rutrum lacus, eget auctor lorem. Vestibulum a tortor vitae.',
            'harga' => 18000,
            'gambar_preview' => 'notes/files/note2.png',
            'files' => [
                ['nama_file' => 'Sistem-Operasi.docx', 'path_file' => 'notes/files/note3.png'],
                ['nama_file' => 'Diagram-Memori.png', 'path_file' => 'notes/files/note4.png'],
            ],
        ],
        [
            'seller_id' => 3,
            'course_id' => 3,
            'judul' => 'Note sample 3',
            'deskripsi' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis vulputate lacus in magna fringilla, at placerat neque tempor. Nam mollis in eros ut convallis. Integer ut rutrum lacus, eget auctor lorem. Vestibulum a tortor vitae.',
            'harga' => 7500,
            'gambar_preview' => 'notes/files/note1.png',
            'files' => [
                ['nama_file' => 'JaringanKomputer.pdf', 'path_file' => 'notes/files/note3.png'],
            ],
        ],
        [
            'seller_id' => 3,
            'course_id' => 3,
            'judul' => 'Note sample 4',
            'deskripsi' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis vulputate lacus in magna fringilla, at placerat neque tempor. Nam mollis in eros ut convallis. Integer ut rutrum lacus, eget auctor lorem. Vestibulum a tortor vitae.',
            'harga' => 7500,
            'gambar_preview' => 'notes/files/note1.png',
            'files' => [
                ['nama_file' => 'JaringanKomputer.pdf', 'path_file' => 'notes/files/note3.png'],
            ],
        ],
        [
            'seller_id' => 4,
            'course_id' => 4,
            'judul' => 'Note sample 5',
            'deskripsi' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis vulputate lacus in magna fringilla, at placerat neque tempor. Nam mollis in eros ut convallis. Integer ut rutrum lacus, eget auctor lorem. Vestibulum a tortor vitae.',
            'harga' => 10000,
            'gambar_preview' => 'notes/files/note2.png',
            'files' => [
                ['nama_file' => 'JaringanKomputer.pdf', 'path_file' => 'notes/files/note4.png'],
            ],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->notes as $noteData) {
            $note = Note::create([
                'seller_id' => $noteData['seller_id'],
                'course_id' => $noteData['course_id'],
                'judul' => $noteData['judul'],
                'deskripsi' => $noteData['deskripsi'],
                'harga' => $noteData['harga'],
                'jumlah_like' => rand(10, 100),
                'jumlah_dikunjungi' => rand(100, 500),
                // Kosongkan dulu, akan diisi setelah lihat file gambarnya
                'gambar_preview' => null,
            ]);

            // Insert tags
            $tagIds = collect(range(1, 5))->shuffle()->take(rand(1, 3));
            NoteTag::insert($tagIds->map(fn($tagId) => [
                'note_id' => $note->note_id,
                'tag_id' => $tagId,
            ])->toArray());

            // Insert files
            $previewPath = null;
            if (isset($noteData['files'])) {
                $noteFiles = collect($noteData['files'])->map(function ($file) use ($note, &$previewPath) {
                    $ext = pathinfo($file['path_file'], PATHINFO_EXTENSION);

                    if (!$previewPath && preg_match('/\.(jpg|jpeg|png)$/i', $file['path_file'])) {
                        $previewPath = $file['path_file'];
                    }

                    return [
                        'note_id' => $note->note_id,
                        'nama_file' => $file['nama_file'],
                        'path_file' => $file['path_file'],
                        'tipe' => $ext,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();

                NoteFile::insert($noteFiles);
            }

            // Update gambar preview
            $note->gambar_preview = $previewPath ?? 'images/default_preview.png';
            $note->save();
        }
    }
}
