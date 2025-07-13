<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Review;
use App\Models\ReviewVote;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function getReviews(Request $request, string $id)
    {
        $user = $request->user();
        try {
            $note = Note::with('reviews.user', 'reviews.response.seller', 'reviews.votes')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan',
                'data' => null
            ], 404);
        }

        $reviews = $note->reviews->map(function ($review) use ($user) {
            // Cek apakah user sudah vote review ini
            $userVote = $user
                ? $review->votes->firstWhere('user_id', $user->user_id)
                : null;

            return [
                'review_id' => $review->review_id,
                'reviewer' => [
                    'reviewer_id' => $review->user->user_id,
                    'nama' => $review->user->nama,
                    'username' => $review->user->username,
                    'foto_profil' => url($review->user->foto_profil_url),
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
                'isVoted' => $userVote ? true : false,
                'voteType' => $userVote->tipe_vote ?? null,
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

    public function voteReview(Request $request, string $id)
    {
        $user = $request->user();
        $request->validate([
            'tipe_vote' => ['required', 'in:like,dislike']
        ]);

        try {
            $review = Review::with('note')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Review tidak ditemukan.',
                'data' => null
            ], 404);
        }

        $existingVote = ReviewVote::where('review_id', $review->review_id)
            ->where('user_id', $user->user_id)
            ->first();

        if ($existingVote) {
            if ($existingVote->tipe_vote === $request->tipe_vote) {
                $review = $review->fresh(['votes', 'user']);
                return response()->json([
                    'success' => false,
                    'message' => 'Kamu sudah melakukan vote ini pada review ini.',
                    'data' => $this->reviewerResponse($review)
                ], 200);
            }
            $existingVote->update(['tipe_vote' => $request->tipe_vote]);
        } else {
            ReviewVote::create([
                'review_id' => $review->review_id,
                'user_id' => $user->user_id,
                'tipe_vote' => $request->tipe_vote,
            ]);
        }

        $review = $review->fresh(['votes', 'user']);
        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambah vote review',
            'data' => $this->reviewerResponse($review)
        ]);
    }

    public function unvoteReview(Request $request, string $id)
    {
        $user = $request->user();

        try {
            $review = Review::with('user')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Review tidak ditemukan.',
                'data' => null
            ], 404);
        }

        $vote = ReviewVote::where('review_id', $review->review_id)
            ->where('user_id', $user->user_id)
            ->first();

        if (!$vote) {
            $review = $review->fresh(['votes', 'user']);
            return response()->json([
                'success' => false,
                'message' => 'Kamu belum melakukan vote pada review ini.',
                'data' => $this->reviewerResponse($review)
            ], 200);
        }

        $vote->delete();
        $review = $review->fresh(['votes', 'user']);
        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus vote review',
            'data' => $this->reviewerResponse($review)
        ]);
    }

    // public function updateReview(Request $request, string $id) {}

    // public function deleteReview(Request $request, string $id) {}

    private function reviewerResponse($review)
    {
        $user = $review->user;
        return [
            'review_id' => $review->review_id,
            'reviewer' => [
                'reviewer_id' => $user->user_id,
                'nama' => $user->nama,
                'username' => $user->username,
                'foto_profil' => url($user->foto_profil_url),
                'rating' => $review->rating,
                'jumlah_like' => $review->like_count,
                'jumlah_dislike' => $review->dislike_count,
            ]
        ];
    }
}
