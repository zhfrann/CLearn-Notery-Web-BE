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

    public function getMostSearchTags(Request $request)
    {
        // Validasi parameter opsional
        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:50', // Default 10, max 50
        ]);

        $limit = $validated['limit'] ?? 10;

        // Query untuk mendapatkan tag paling banyak digunakan
        $popularTags = Tag::withCount(['noteTags' => function ($query) {
            // Hanya hitung notes yang sudah diterima/approved
            $query->whereHas('note.noteStatus', function ($q) {
                $q->where('status', 'diterima');
            });
        }])
            ->having('note_tags_count', '>', 0) // Hanya tag yang punya minimal 1 note
            ->orderByDesc('note_tags_count')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data tag paling populer',
            'data' => $popularTags->map(function ($tag) {
                return [
                    'tag_id' => $tag->tag_id,
                    'nama_tag' => $tag->nama_tag,
                    // 'jumlah_notes' => $tag->note_tags_count,
                ];
            })
        ]);
    }
}
