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

    public function getTagsDetail(Request $request, string $id)
    {
        try {
            $tag = Tag::find($id);

            if (!$tag) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tag tidak ditemukan',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail tag berhasil diambil',
                'data' => [
                    'tag_id' => $tag->tag_id,
                    'nama_tag' => $tag->nama_tag,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail tag',
                'data' => null
            ], 500);
        }
    }
}
