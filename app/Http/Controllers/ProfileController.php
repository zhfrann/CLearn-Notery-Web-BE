<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function getProfileDetail(Request $request) {}

    public function updateProfile(Request $request) {}

    public function getNotes(Request $request)
    {
        $user = $request->user();

        // Notes yang dijual user
        $notesDijual = $user->notes()
            ->with(['noteTags.tag'])
            ->withCount('transactions')
            ->get()
            ->map(function ($note) {
                return [
                    'note_id' => $note->note_id,
                    'seller' => [
                        'seller_id' => $note->seller_id,
                        'seller_name' => $note->seller->name,
                        'seller_username' => $note->seller->username,
                    ],
                    'judul' => $note->judul,
                    'deskripsi' => $note->deskripsi,
                    'harga' => $note->harga,
                    'jumlah_like' => $note->jumlah_like,
                    'jumlah_favorit' => $note->savedByUsers->count(),
                    'jumlah_dikunjungi' => $note->jumlah_dikunjungi,
                    'jumlah_terjual' => $note->transactions_count,
                    'rating' => round($note->reviews->avg('rating') ?? 0, 2),
                    'gambar_preview' => url(asset('storage/' . $note->gambar_preview)),
                    'tags' => $note->noteTags->pluck('tag.nama_tag'),
                    'created_at' => $note->created_at->toIso8601String(),
                ];
            });

        // Notes yang dibeli user
        $notesDibeli = Transaction::where('buyer_id', $user->user_id)
            ->with(['note.noteTags.tag', 'note.reviews', 'note.savedByUsers', 'note.transactions'])
            ->get()
            ->map(function ($transaction) {
                $note = $transaction->note;
                return [
                    'note_id' => $note->note_id,
                    'seller' => [
                        'seller_id' => $note->seller_id,
                        'seller_name' => $note->seller->name,
                        'seller_username' => $note->seller->username,
                    ],
                    'judul' => $note->judul,
                    'deskripsi' => $note->deskripsi,
                    'harga' => $note->harga,
                    'jumlah_like' => $note->jumlah_like,
                    'jumlah_favorit' => $note->savedByUsers->count(),
                    'jumlah_dikunjungi' => $note->jumlah_dikunjungi,
                    'jumlah_terjual' => $note->transactions->count(),
                    'rating' => round($note->reviews->avg('rating') ?? 0, 2),
                    'gambar_preview' => url(asset('storage/' . $note->gambar_preview)),
                    'tags' => $note->noteTags->pluck('tag.nama_tag'),
                    'created_at' => $note->created_at->toIso8601String(),
                ];
            });

        // Notes yang difavoritkan user (saved notes)
        $favoriteNotes = $user->savedNotes()
            ->with(['note.noteTags.tag', 'note.reviews', 'note.savedByUsers', 'note.transactions'])
            ->get()
            ->map(function ($saved) {
                $note = $saved->note;
                return [
                    'note_id' => $note->note_id,
                    'seller' => [
                        'seller_id' => $note->seller_id,
                        'seller_name' => $note->seller->name,
                        'seller_username' => $note->seller->username,
                    ],
                    'judul' => $note->judul,
                    'deskripsi' => $note->deskripsi,
                    'harga' => $note->harga,
                    'jumlah_like' => $note->jumlah_like,
                    'jumlah_favorit' => $note->savedByUsers->count(),
                    'jumlah_dikunjungi' => $note->jumlah_dikunjungi,
                    'jumlah_terjual' => $note->transactions->count(),
                    'rating' => round($note->reviews->avg('rating') ?? 0, 1),
                    'gambar_preview' => url(asset('storage/' . $note->gambar_preview)),
                    'tags' => $note->noteTags->pluck('tag.nama_tag'),
                    'created_at' => $note->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Catatan profil pengguna',
            'data' => [
                'notes_dijual' => $notesDijual,
                'notes_dibeli' => $notesDibeli,
                'favorite' => $favoriteNotes,
            ],
        ]);
    }

    // public function updatePhoto(Request $request) {}

    // public function changePassword(Request $request) {}
}
