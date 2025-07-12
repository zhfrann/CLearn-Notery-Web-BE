<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileDataController extends Controller
{
    public function productStatus(Request $request) {
        $user = $request->user();

        $menunggu = $user->notes()->where('status', 'menunggu')->get(['note_id', 'judul', 'gambar_preview', 'created_at']);
        $diterima = $user->notes()->where('status', 'diterima')->get(['note_id', 'judul', 'gambar_preview', 'created_at']);
        $ditolak = $user->notes()->where('status', 'ditolak')->get(['note_id', 'judul', 'gambar_preview', 'created_at']);

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

    public function transactions(Request $request) {}

    public function favoritesNotes(Request $request) {}
}
