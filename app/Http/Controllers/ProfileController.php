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

        // Helper untuk format data note
        $formatNote = function ($note) use ($user) {
            return [
                'note_id' => $note->note_id,
                'seller' => [
                    'seller_id' => $note->seller->user_id ?? $note->seller_id,
                    'name' => $note->seller->nama ?? null,
                    'username' => $note->seller->username ?? null,
                    'foto_profil' => url($note->seller->foto_profil_url),
                    'isTopCreator' => null,
                ],
                'judul' => $note->judul,
                'deskripsi' => $note->deskripsi,
                'harga' => $note->harga,
                'jumlah_like' => $note->jumlah_like,
                'jumlah_favorit' => $note->savedByUsers->count(),
                'jumlah_dikunjungi' => $note->jumlah_dikunjungi,
                'jumlah_terjual' => $note->transactions->where('status', 'success')->count(),
                'rating' => round($note->reviews->avg('rating') ?? 0, 2),
                'gambar_preview' => asset('storage/' . $note->gambar_preview),
                'tags' => $note->noteTags->pluck('tag.nama_tag'),
                'isLiked' => $user ? $note->likes->contains('user_id', $user->user_id) : false,
                'isFavorite' => $user ? $note->savedByUsers->contains('user_id', $user->user_id) : false,
                'created_at' => $note->created_at->toIso8601String(),
            ];
        };

        // Notes dijual oleh user (note status harus diterima)
        $notesDijual = $user->notes()
            ->whereHas('noteStatus', fn($q) => $q->where('status', 'diterima'))
            ->with(['seller', 'noteTags.tag', 'reviews', 'likes', 'savedByUsers', 'transactions'])
            ->get()
            ->map($formatNote);

        // Notes dibeli oleh user (hanya transaksi yang success dan note diterima)
        $notesDibeli = Transaction::where('buyer_id', $user->user_id)
            ->where('status', 'success')
            ->whereHas('note.noteStatus', fn($q) => $q->where('status', 'diterima'))
            ->with(['note.seller', 'note.noteTags.tag', 'note.reviews', 'note.likes', 'note.savedByUsers', 'note.transactions'])
            ->get()
            ->map(fn($tx) => $formatNote($tx->note));

        // Notes difavoritkan user (saved_notes), hanya note yang diterima
        $favoriteNotes = $user->savedNotes()
            ->whereHas('note.noteStatus', fn($q) => $q->where('status', 'diterima'))
            ->with(['note.seller', 'note.noteTags.tag', 'note.reviews', 'note.likes', 'note.savedByUsers', 'note.transactions'])
            ->get()
            ->map(fn($saved) => $formatNote($saved->note));

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
