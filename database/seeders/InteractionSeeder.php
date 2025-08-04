<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\NoteLike;
use App\Models\Review;
use App\Models\ReviewResponse;
use App\Models\ReviewVote;
use App\Models\SavedNote;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InteractionSeeder extends Seeder
{
    private $sampleBuktiPembayaran = [
        'bukti_pembayaran/bukti1.jpg',
        'bukti_pembayaran/bukti2.jpg',
        'bukti_pembayaran/bukti3.jpg',
        'bukti_pembayaran/bukti4.jpg',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $notes = Note::all();

        foreach ($notes as $note) {
            $eligibleUsers = $users->where('user_id', '!=', $note->seller_id)->where('role', 'student');

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

            // ====== TRANSAKSI ======
            $takeTrans = min($eligibleUsers->count(), rand(1, 5));
            if ($takeTrans > 0) {
                $buyerIds = $eligibleUsers->random($takeTrans)->pluck('user_id');
                foreach ($buyerIds as $buyerId) {
                    $buktiBayar = collect($this->sampleBuktiPembayaran)->random();

                    Transaction::create([
                        'note_id' => $note->note_id,
                        'buyer_id' => $buyerId,
                        'status' => 'paid',
                        'tgl_transaksi' => now()->subDays(rand(1, 30)),
                        'catatan' => fake()->sentence(),
                        'bukti_pembayaran' => $buktiBayar,

                        // Calculate all fees
                        // $notePrice = $note->harga;
                        // $platformFeeRate = 0.10; // 10%
                        // $midtransFeeRate = 0.029; // 2.9%
                        // $midtransFixedFee = 2000; // Rp 2,000
                        // $grossAmount = $notePrice + $platformFee + $midtransFee;
                        'jumlah' => ($note->harga + round($note->harga * 0.10) + round(($note->harga * 0.029) + 2000)),
                        'platform_fee' => round($note->harga * 0.10),
                        'seller_amount' => $note->harga
                    ]);
                }
            }

            // ====== REVIEW ======
            $takeReview = min($eligibleUsers->count(), rand(1, 5));
            if ($takeReview > 0) {
                $reviewerIds = $eligibleUsers->random($takeReview)->pluck('user_id');
                foreach ($reviewerIds as $reviewerId) {
                    $review = Review::create([
                        'user_id' => $reviewerId,
                        'note_id' => $note->note_id,
                        'komentar' => fake()->sentence(),
                        'rating' => rand(3, 5),
                        'tgl_review' => now()->subDays(rand(1, 30)),
                    ]);

                    // Seeder untuk review_votes (like/dislike review)
                    $voters = $eligibleUsers->where('user_id', '!=', $reviewerId)->shuffle()->take(rand(0, 3));
                    foreach ($voters as $voter) {
                        ReviewVote::create([
                            'review_id' => $review->review_id,
                            'user_id' => $voter->user_id,
                            'tipe_vote' => fake()->randomElement(['like', 'dislike']),
                        ]);
                    }

                    if (fake()->boolean(50)) { // 50% kemungkinan review diberi respon
                        ReviewResponse::create([
                            'review_id' => $review->review_id,
                            'seller_id' => $note->seller_id,
                            'respon' => fake()->sentence(),
                            'tgl_respon' => now()->subDays(rand(0, 10)),
                        ]);
                    }
                }
            }
        }
    }
}
