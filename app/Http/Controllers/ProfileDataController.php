<?php

namespace App\Http\Controllers;

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

    public function transactions(Request $request)
    {
        $user = $request->user();

        $transactions = Transaction::where('buyer_id', $user->user_id)
            ->with(['note.noteTags.tag'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($transaction) {
                return [
                    'transaction_id' => $transaction->transaction_id,
                    'note_id' => $transaction->note->note_id,
                    'judul' => $transaction->note->judul,
                    'tags' => $transaction->note->noteTags->pluck('tag.nama_tag'),
                    'harga' => $transaction->note->harga,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Daftar transaksi pengguna',
            'data' => $transactions
        ]);
    }

    public function transactionDetail(Request $request, string $id)
    {
        $user = $request->user();

        try {
            $transaction = Transaction::where('transaction_id', $id)
                ->where('buyer_id', $user->user_id)
                ->with(['note.course.major.faculty', 'note.reviews'])
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        // Cek apakah user sudah review note ini
        $isReviewed = $transaction->note->reviews()
            ->where('user_id', $user->user_id)
            ->exists();

        return response()->json([
            'success' => true,
            'message' => 'Detail transaksi pengguna',
            'data' => [
                'transaction_id' => $transaction->transaction_id,
                'note_id' => $transaction->note->note_id,
                'judul' => $transaction->note->judul,
                'harga' => $transaction->note->harga,
                'status' => $transaction->status,
                'fakultas' => [
                    'faculty_id' => $transaction->note->course->major->faculty->faculty_id,
                    'nama_fakultas' => $transaction->note->course->major->faculty->nama_fakultas,
                ],
                'program_studi' => [
                    'major_id' => $transaction->note->course->major->major_id,
                    'nama_program_studi' => $transaction->note->course->major->nama_program_studi,
                ],
                'bukti_pembayaran_url' => $transaction->bukti_pembayaran ?
                    url('storage/' . $transaction->bukti_pembayaran) : null,
                'nominal' => $transaction->note->harga,
                'isReviewed' => $isReviewed,
                'created_at' => $transaction->created_at->toIso8601String(),
            ]
        ]);
    }

    // public function favoritesNotes(Request $request) {}
}
