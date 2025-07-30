<?php

namespace App\Http\Controllers;

use App\Models\ReviewVote;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Review;

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

    public function createReview(Request $request, string $id)
    {
        $request->validate([
            'komentar' => 'required|string',
            'rating' => 'required|numeric|min:0|max:5',
        ]);

        try {
            $note = Note::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan',
                'data' => null
            ], 404);
        }

        // Cek apakah user sudah pernah review note ini
        // $existingReview = Review::where('note_id', $note->note_id)
        //     ->where('user_id', $request->user()->user_id)
        //     ->first();

        // if ($existingReview) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Anda sudah memberikan review untuk note ini',
        //         'data' => null
        //     ], 400);
        // }

        $review = Review::create([
            'note_id' => $note->note_id,
            'user_id' => $request->user()->user_id,
            'komentar' => $request->komentar,
            'rating' => $request->rating,
            'tgl_review' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambah ulasan note',
            'data' => [
                'review_id' => $review->review_id,
                'note_id' => $note->note_id,
                'judul' => $note->judul,
                'komentar' => $review->komentar,
                'rating' => $review->rating,
                'tgl_review' => $review->tgl_review,
            ]
        ]);
    }

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

    public function createSellerResponse(Request $request, $id)
    {
        $seller = auth()->user();

        $request->validate([
            'respon' => 'required|string',
        ]);

        $review = Review::query()->findOrFail($id);

        // Pastikan hanya seller dari note terkait yang bisa merespon
        $note = $review->note;
        if ($note->seller_id !== $seller->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak merespon review ini karena anda bukan seller note ini.',
            ], 403);
        }

        // Cek jika sudah ada response
        if ($review->response) {
            return response()->json([
                'success' => false,
                'message' => 'Review sudah direspon sebelumnya.',
            ], 400);
        }

        $response = $review->response()->create([
            'seller_id' => $seller->user_id,
            'respon' => $request->respon,
            'tgl_respon' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil membuat respon ulasan',
            'data' => [
                'review_response_id' => $response->review_response_id,
                'review_id' => $response->review_id,
                'seller_id' => $response->seller_id,
                'reviewer_id' => $review->user_id,
                'respons' => $response->respon,
            ]
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
