<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function getProfileDetail(Request $request)
    {
        $user = User::query()->where('user_id', $request->user()->user_id)->first();

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => new UserResource($user->load(['semester', 'major', 'faculty', 'notes', 'transactions', 'favoriteCourses.course']))
        ]);
    }

    public function getQrCode(Request $request)
    {
        $user = $request->user();

        return response()->json([
            "success" => true,
            "message" => "QR Code user",
            "data" => [
                "user_id" => $user->user_id,
                "username" => $user->username,
                "nama" => $user->nama,
                "qr_code_url" => $user->qr_code ? url("storage/" . $user->qr_code) : null
            ]
        ]);
    }

    public function uploadQrCode(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'qr_code' => 'required|image|mimes:png,jpg,jpeg|max:10240'
        ]);

        // Hapus QR code lama jika ada
        if ($user->qr_code && Storage::disk('public')->exists($user->qr_code)) {
            Storage::disk('public')->delete($user->qr_code);
        }

        // Upload QR code baru
        $file = $request->file('qr_code');
        $filename = $user->username . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('qr_code', $filename, 'public');

        // Update user
        $user->update(['qr_code' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'QR Code berhasil diupload',
            'data' => [
                "user_id" => $user->user_id,
                "username" => $user->username,
                "nama" => $user->nama,
                'qr_code_url' => url('storage/' . $path)
            ]
        ]);
    }

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
            ->where('status', 'paid')
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
