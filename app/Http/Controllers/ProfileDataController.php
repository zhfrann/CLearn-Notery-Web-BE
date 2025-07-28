<?php

namespace App\Http\Controllers;

use App\Models\NoteStatus;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ProfileDataController extends Controller
{
    public function productStatus(Request $request)
    {
        $user = $request->user();

        $menunggu = $user->notes()
            ->whereHas('noteStatus', fn($q) => $q->where('status', 'menunggu'))
            ->get(['note_id', 'judul', 'gambar_preview', 'created_at']);

        $diterima = $user->notes()
            ->whereHas('noteStatus', fn($q) => $q->where('status', 'diterima'))
            ->get(['note_id', 'judul', 'gambar_preview', 'created_at']);

        $ditolak = $user->notes()
            ->whereHas('noteStatus', fn($q) => $q->where('status', 'ditolak'))
            ->get(['note_id', 'judul', 'gambar_preview', 'created_at']);

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => [
                'menunggu' => $menunggu,
                'diterima' => $diterima,
                'ditolak' => $ditolak,
            ]
        ]);
    }

    public function productStatusDetail(Request $request, string $id)
    {
        $user = $request->user();

        try {
            $noteStatus = NoteStatus::with(['note.seller'])
                ->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Status produk tidak ditemukan',
                'data' => null
            ], 404);
        }

        // Cek apakah user adalah pemilik note
        if ($noteStatus->note->seller_id !== $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melihat status produk ini',
                'data' => null
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail status produk berhasil diambil',
            'data' => [
                'note_status_id' => $noteStatus->note_status_id,
                'note_id' => $noteStatus->note_id,
                'judul' => $noteStatus->note->judul,
                'seller' => [
                    'seller_id' => $noteStatus->note->seller->user_id,
                    'nama' => $noteStatus->note->seller->nama,
                    'username' => $noteStatus->note->seller->username,
                    'foto_profil' => $noteStatus->note->seller->foto_profil ?
                        url('storage/' . $noteStatus->note->seller->foto_profil) : null,
                ],
                'status' => $noteStatus->status,
                'catatan' => $noteStatus->catatan,
            ]
        ]);
    }

    public function transactions(Request $request)
    {
        $user = $request->user();

        // Transaksi sebagai buyer
        $buyerTransactions = Transaction::where('buyer_id', $user->user_id)
            ->with(['note.seller', 'note.noteTags.tag'])
            ->get()
            ->map(function ($transaction) use ($user) {
                return [
                    'transaction_id' => $transaction->transaction_id,
                    'note_id' => $transaction->note->note_id,
                    'judul' => $transaction->note->judul,
                    'tags' => $transaction->note->noteTags->pluck('tag.nama_tag'),
                    'harga' => $transaction->note->harga,
                    'status' => $transaction->status,
                    'role' => 'buyer', // User sebagai pembeli
                    'other_party' => [
                        'user_id' => $transaction->note->seller->user_id,
                        'nama' => $transaction->note->seller->nama,
                        'username' => $transaction->note->seller->username,
                    ],
                    'created_at' => $transaction->created_at->toIso8601String(),
                ];
            });

        // Transaksi sebagai seller
        $sellerTransactions = Transaction::whereHas('note', function ($q) use ($user) {
            $q->where('seller_id', $user->user_id);
        })
            ->with(['buyer', 'note.noteTags.tag'])
            ->get()
            ->map(function ($transaction) use ($user) {
                return [
                    'transaction_id' => $transaction->transaction_id,
                    'note_id' => $transaction->note->note_id,
                    'judul' => $transaction->note->judul,
                    'tags' => $transaction->note->noteTags->pluck('tag.nama_tag'),
                    'harga' => $transaction->note->harga,
                    'status' => $transaction->status,
                    'role' => 'seller', // User sebagai penjual
                    'other_party' => [
                        'user_id' => $transaction->buyer->user_id,
                        'nama' => $transaction->buyer->nama,
                        'username' => $transaction->buyer->username,
                    ],
                    'created_at' => $transaction->created_at->toIso8601String(),
                ];
            });

        // Gabungkan dan urutkan berdasarkan waktu terbaru
        $allTransactions = $buyerTransactions->concat($sellerTransactions)
            ->sortByDesc('created_at')
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat transaksi pengguna',
            'data' => $allTransactions
        ]);
    }

    public function transactionDetail(Request $request, string $id)
    {
        $user = $request->user();

        // Cari transaksi dimana user adalah buyer ATAU seller
        $transaction = Transaction::where('transaction_id', $id)
            ->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->user_id)
                    ->orWhereHas('note', function ($subQ) use ($user) {
                        $subQ->where('seller_id', $user->user_id);
                    });
            })
            ->with(['note.course.major.faculty', 'note.reviews', 'buyer', 'note.seller'])
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        // Tentukan role user
        $userRole = $transaction->buyer_id === $user->user_id ? 'buyer' : 'seller';

        // Tentukan pihak lawan
        $otherParty = $userRole === 'buyer'
            ? $transaction->note->seller
            : $transaction->buyer;

        // Cek apakah user sudah review note ini (hanya untuk buyer)
        $isReviewed = $userRole === 'buyer'
            ? $transaction->note->reviews()->where('user_id', $user->user_id)->exists()
            : null;

        return response()->json([
            'success' => true,
            'message' => 'Detail transaksi',
            'data' => [
                'transaction_id' => $transaction->transaction_id,
                'note_id' => $transaction->note->note_id,
                'judul' => $transaction->note->judul,
                'harga' => $transaction->note->harga,
                'status' => $transaction->status,
                'role' => $userRole,
                'other_party' => [
                    'user_id' => $otherParty->user_id,
                    'nama' => $otherParty->nama,
                    'username' => $otherParty->username,
                ],
                'fakultas' => [
                    'faculty_id' => $transaction->note->course->major->faculty->faculty_id,
                    'nama_fakultas' => $transaction->note->course->major->faculty->nama_fakultas,
                ],
                'program_studi' => [
                    'major_id' => $transaction->note->course->major->major_id,
                    'nama_program_studi' => $transaction->note->course->major->nama_program_studi,
                ],
                'nominal' => $transaction->note->harga,
                'isReviewed' => $isReviewed, // null untuk seller
                'created_at' => $transaction->created_at->toIso8601String(),
            ]
        ]);
    }

    // public function favoritesNotes(Request $request) {}
}
