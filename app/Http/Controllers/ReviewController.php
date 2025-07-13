<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function getReviews(Request $request, string $id)
    {
        try {
            $note = Note::with('reviews.user', 'reviews.response.seller')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan',
                'data' => null
            ], 404);
        }

        $reviews = $note->reviews->map(function ($review) {
            return [
                'review_id' => $review->review_id,
                'reviewer' => [
                    'reviewer_id' => $review->user->user_id,
                    'nama' => $review->user->nama,
                    'username' => $review->user->username,
                    'rating' => $review->rating,
                    'jumlah_like' => $review->like_count,
                    'jumlah_dislike' => $review->dislike_count,
                    'review' => $review->komentar,
                    'seller_response' => $review->response ? [
                        'seller_id' => $review->response->seller->user_id,
                        'nama' => $review->response->seller->nama,
                        'email' => $review->response->seller->email,
                        'foto_profil' => url($review->response->seller->foto_profil_url),
                        'response' => $review->response->respon,
                    ] : null,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar review note',
            'data' => [
                'note_id' => $note->note_id,
                'judul' => $note->judul,
                'reviews' => $reviews,
            ]
        ]);
    }

    public function createReview(Request $request, string $id) {}

    // public function updateReview(Request $request, string $id) {}

    // public function deleteReview(Request $request, string $id) {}
}
