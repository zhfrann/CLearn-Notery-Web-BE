<?php

namespace App\Http\Controllers;

use App\Models\NoteStatus;
use App\Models\Transaction;
use App\Models\WithdrawRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
                    'gambar_preview' => url(asset('storage/' . $transaction->note->gambar_preview)),
                    // 'other_party' => [
                    //     'user_id' => $transaction->note->seller->user_id,
                    //     'nama' => $transaction->note->seller->nama,
                    //     'username' => $transaction->note->seller->username,
                    // ],
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
                    'gambar_preview' => url(asset('storage/' . $transaction->note->gambar_preview)),
                    // 'other_party' => [
                    //     'user_id' => $transaction->buyer->user_id,
                    //     'nama' => $transaction->buyer->nama,
                    //     'username' => $transaction->buyer->username,
                    // ],
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

    public function earning(Request $request)
    {
        $user = $request->user();

        // Ambil semua transaksi sukses (paid) untuk note milik user (seller)
        $transactions = Transaction::whereHas('note', function ($q) use ($user) {
            $q->where('seller_id', $user->user_id);
        })
            ->where('status', 'paid')
            ->orderBy('tgl_transaksi', 'asc')
            ->get();

        $totalIncome = $transactions->sum('seller_amount');
        $totalSales = $transactions->count();

        // Total withdraw yang sudah diterima/diproses
        $withdrawn = WithdrawRequest::where('user_id', $user->user_id)
            ->where('status', '=', 'diterima_admin')
            ->sum('jumlah');

        // Saldo yang bisa ditarik
        $currentIncome = $totalIncome - $withdrawn;
        $currentIncome = max($currentIncome, 0);

        // Rata-rata bulanan
        $first = $transactions->first();
        $last = $transactions->last();
        if ($first && $last) {
            $firstMonth = Carbon::parse($first->tgl_transaksi)->startOfMonth();
            $lastMonth = Carbon::parse($last->tgl_transaksi)->startOfMonth();
            $months = $firstMonth->diffInMonths($lastMonth) + 1;
        } else {
            $months = 1;
        }
        $avgMonthlyIncome = $months > 0 ? round($totalIncome / $months) : 0;
        $avgMonthlySales = $months > 0 ? round($totalSales / $months) : 0;

        // Rata-rata harian
        if ($first && $last) {
            $firstDay = Carbon::parse($first->tgl_transaksi)->startOfDay();
            $lastDay = Carbon::parse($last->tgl_transaksi)->startOfDay();
            $days = $firstDay->diffInDays($lastDay) + 1;
        } else {
            $days = 1;
        }
        $avgDailyIncome = $days > 0 ? round($totalIncome / $days) : 0;
        $avgDailySales = $days > 0 ? round($totalSales / $days) : 0;

        return response()->json([
            'success' => true,
            'message' => 'Earning summary',
            'data' => [
                'total_income' => $totalIncome,
                'total_sales' => $totalSales,
                'current_income' => $currentIncome,
                'withdrawn' => $withdrawn,
                'avg_monthly_income' => $avgMonthlyIncome,
                'avg_monthly_sales' => $avgMonthlySales,
                'avg_daily_income' => $avgDailyIncome,
                'avg_daily_sales' => $avgDailySales,
            ]
        ]);
    }

    public function withdraw(Request $request)
    {
        $user = $request->user();

        // Validasi jumlah penarikan
        $request->validate([
            'jumlah' => 'required|integer|min:10000', // minimal penarikan 10.000
        ]);

        $jumlah = $request->input('jumlah');

        // Hitung current income (saldo yang bisa ditarik)
        $transactions = Transaction::whereHas('note', function ($q) use ($user) {
            $q->where('seller_id', $user->user_id);
        })
            ->where('status', 'paid')
            ->get();

        $totalIncome = $transactions->sum('seller_amount');
        $withdrawn = WithdrawRequest::where('user_id', $user->user_id)
            ->where('status', '=', 'diterima_admin')
            ->sum('jumlah');
        $currentIncome = $totalIncome - $withdrawn;

        // Cek saldo cukup
        if ($jumlah > $currentIncome) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak mencukupi untuk penarikan ini.',
            ], 400);
        }

        // Buat withdraw request baru
        $withdraw = WithdrawRequest::create([
            'user_id' => $user->user_id,
            'jumlah' => $jumlah,
            'status' => 'menunggu',
            'tgl_request' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan penarikan berhasil, menunggu persetujuan admin.',
            'data' => [
                'withdraw_request_id' => $withdraw->withdraw_request_id,
                'user_id' => $user->user_id,
                'username' => $user->username,
                'jumlah' => $withdraw->jumlah,
                'status' => $withdraw->status,
                'tgl_request' => $withdraw->tgl_request,
            ]
        ]);
    }
}
