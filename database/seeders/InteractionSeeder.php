<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\Review;
use App\Models\SavedNote;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $notes = Note::all();

        foreach ($notes as $note) {
            $favoritUserIds = $users->where('user_id', '!=', $note->seller_id)->random(rand(1, 3))->pluck('user_id');

            // Simpan sebagai favorit
            foreach ($favoritUserIds as $userId) {
                SavedNote::create([
                    'note_id' => $note->note_id,
                    'user_id' => $userId,
                ]);
            }

            // Transaksi acak
            $jumlahTerjual = rand(1, 5);
            $buyerIds = $users->where('user_id', '!=', $note->seller_id)->random(min($jumlahTerjual, $users->count() - 1))->pluck('user_id');

            foreach ($buyerIds as $buyerId) {
                Transaction::create([
                    'note_id' => $note->note_id,
                    'buyer_id' => $buyerId,
                    'status' => 'selesai',
                    'tgl_transaksi' => now()->subDays(rand(1, 30)),
                    'catatan' => fake()->sentence(),
                ]);

                Review::create([
                    'user_id' => $buyerId,
                    'note_id' => $note->note_id,
                    'komentar' => fake()->sentence(),
                    'rating' => rand(3, 5),
                    'tgl_review' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}
