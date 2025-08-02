<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Contoh data pengumuman
        $announcements = [
            [
                'title' => 'Selamat Datang di Notery!',
                'body' => 'Platform jual beli catatan kuliah kini hadir untuk kamu. Jangan lupa lengkapi profilmu!',
            ],
            [
                'title' => 'Fitur Baru: Chat Langsung',
                'body' => 'Kini kamu bisa chat langsung dengan penjual sebelum membeli catatan.',
            ]
        ];

        foreach ($announcements as $data) {
            Notification::create([
                'user_id' => null,
                'type' => 'announcement',
                'title' => $data['title'],
                'body' => $data['body'],
                'is_read' => false,
            ]);
        }
    }
}
