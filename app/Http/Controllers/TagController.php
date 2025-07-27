<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function getTags(Request $request)
    {
        try {
            $tags = Tag::select('tag_id', 'nama_tag')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil daftar tags',
                'data' => $tags
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar tags',
                'data' => []
            ], 500);
        }
    }
}
