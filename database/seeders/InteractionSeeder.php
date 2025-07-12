<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\NoteLike;
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
            $eligibleUsers = $users->where('user_id', '!=', $note->seller_id);

            // ====== FAVORIT ======
            $takeFav = min($eligibleUsers->count(), rand(1, 3));
            if ($takeFav > 0) {
                $favoritUserIds = $eligibleUsers->random($takeFav)->pluck('user_id');
                foreach ($favoritUserIds as $userId) {
                    SavedNote::firstOrCreate([
                        'note_id' => $note->note_id,
                        'user_id' => $userId,
                    ]);
                }
            }

            // ====== LIKE ======
            $takeLike = min($eligibleUsers->count(), rand(1, 5));
            if ($takeLike > 0) {
                $likeUserIds = $eligibleUsers->random($takeLike)->pluck('user_id');
                foreach ($likeUserIds as $userId) {
                    NoteLike::firstOrCreate([
                        'note_id' => $note->note_id,
                        'user_id' => $userId,
                    ]);
                }
            }

            // ====== TRANSAKSI & REVIEW ======
            $takeTrans = min($eligibleUsers->count(), rand(1, 5));
            if ($takeTrans > 0) {
                $buyerIds = $eligibleUsers->random($takeTrans)->pluck('user_id');
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
}
