<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Review;

class ReviewController extends Controller
{
    public function getReviews(Request $request, string $id) {}

    public function createReview(Request $request, string $id) 
    {
        $request->validate([
        'review' => 'required|string',
        'rating' => 'required|numeric|min:0|max:5',
        ]);

        $note = Note::findOrFail($id);

        $review = Review::create([
            'note_id' => $note->id,
            'user_id' => auth()->id(), 
            'review' => $request->review,
            'rating' => $request->rating,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambah ulasan note',
            'data' => [
                'note_id' => $note->id,
                'judul' => $note->judul,
                'review' => $review->review,
                'rating' => $review->rating,
            ]
        ]);
    }

    public function updateReview(Request $request, string $id) {}

    public function deleteReview(Request $request, string $id) {}
}
