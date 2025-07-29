<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Note;
use App\Models\Semester;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Log;
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

        // Validasi query parameters untuk filtering
        $validated = $request->validate([
            'nama' => 'nullable|string|max:255',
            'course_id' => 'nullable|exists:courses,course_id',
            'faculty_id' => 'nullable|exists:faculties,faculty_id',
            'major_id' => 'nullable|exists:majors,major_id',
            'semester_id' => 'nullable|exists:semesters,semester_id',
            'rating' => 'nullable|numeric|min:1|max:5',
            'tag_names' => 'nullable|array',
            'tag_names.*' => 'string|max:255',
            'size' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        $size = $validated['size'] ?? 15;
        $page = $validated['page'] ?? 1;

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
                'isBuy' => $user ? !Transaction::where('note_id', $note->note_id)
                    ->where('buyer_id', $user->user_id)
                    ->where('status', 'paid')
                    ->exists() : false,
                'created_at' => $note->created_at->toIso8601String(),
            ];
        };

        // 1. Notes dijual (yang BISA DIBELI oleh user) - dengan filtering
        $notesDijualQuery = Note::whereHas('noteStatus', fn($q) => $q->where('status', 'diterima'))
            ->with(['seller', 'noteTags.tag', 'reviews', 'likes', 'savedByUsers', 'transactions', 'course.semester.major.faculty']);

        // Apply filtering pada notes yang bisa dibeli
        if (!empty($validated['nama'])) {
            $notesDijualQuery->where('judul', 'LIKE', '%' . $validated['nama'] . '%');
        }

        if (!empty($validated['course_id'])) {
            $notesDijualQuery->where('course_id', $validated['course_id']);
        }

        if (!empty($validated['faculty_id'])) {
            $notesDijualQuery->whereHas('course.semester.major.faculty', function ($q) use ($validated) {
                $q->where('faculty_id', $validated['faculty_id']);
            });
        }

        if (!empty($validated['major_id'])) {
            $notesDijualQuery->whereHas('course.semester.major', function ($q) use ($validated) {
                $q->where('major_id', $validated['major_id']);
            });
        }

        if (!empty($validated['semester_id'])) {
            $notesDijualQuery->whereHas('course.semester', function ($q) use ($validated) {
                $q->where('semester_id', $validated['semester_id']);
            });
        }

        if (!empty($validated['rating'])) {
            $notesDijualQuery->whereIn('note_id', function ($query) use ($validated) {
                $query->select('note_id')
                    ->from('reviews')
                    ->groupBy('note_id')
                    ->havingRaw('AVG(rating) >= ?', [$validated['rating']]);
            });
        }

        if (!empty($validated['tag_names']) && is_array($validated['tag_names'])) {
            $notesDijualQuery->whereHas('noteTags.tag', function ($q) use ($validated) {
                $q->whereIn('nama_tag', $validated['tag_names']);
            });
        }

        $paginatedNotesDijual = $notesDijualQuery->orderBy('created_at', 'desc')->paginate($size, ['*'], 'page', $page);
        $notesDijual = $paginatedNotesDijual->getCollection()->map($formatNote);

        // 2. Notes dibeli oleh user (dengan filtering)
        $notesDibeliQuery = Transaction::where('buyer_id', $user->user_id)
            ->where('status', 'paid')
            ->whereHas('note.noteStatus', fn($q) => $q->where('status', 'diterima'))
            ->with(['note.seller', 'note.noteTags.tag', 'note.reviews', 'note.likes', 'note.savedByUsers', 'note.transactions', 'note.course.semester.major.faculty']);

        // Apply filtering pada notes yang dibeli
        if (!empty($validated['nama'])) {
            $notesDibeliQuery->whereHas('note', function ($q) use ($validated) {
                $q->where('judul', 'LIKE', '%' . $validated['nama'] . '%');
            });
        }

        if (!empty($validated['course_id'])) {
            $notesDibeliQuery->whereHas('note', function ($q) use ($validated) {
                $q->where('course_id', $validated['course_id']);
            });
        }

        if (!empty($validated['faculty_id'])) {
            $notesDibeliQuery->whereHas('note.course.semester.major.faculty', function ($q) use ($validated) {
                $q->where('faculty_id', $validated['faculty_id']);
            });
        }

        if (!empty($validated['major_id'])) {
            $notesDibeliQuery->whereHas('note.course.semester.major', function ($q) use ($validated) {
                $q->where('major_id', $validated['major_id']);
            });
        }

        if (!empty($validated['semester_id'])) {
            $notesDibeliQuery->whereHas('note.course.semester', function ($q) use ($validated) {
                $q->where('semester_id', $validated['semester_id']);
            });
        }

        if (!empty($validated['rating'])) {
            $notesDibeliQuery->whereIn('note_id', function ($query) use ($validated) {
                $query->select('note_id')
                    ->from('reviews')
                    ->groupBy('note_id')
                    ->havingRaw('AVG(rating) >= ?', [$validated['rating']]);
            });
        }

        if (!empty($validated['tag_names']) && is_array($validated['tag_names'])) {
            $notesDibeliQuery->whereHas('note.noteTags.tag', function ($q) use ($validated) {
                $q->whereIn('nama_tag', $validated['tag_names']);
            });
        }

        $paginatedNotesDibeli = $notesDibeliQuery->orderBy('created_at', 'desc')->paginate($size, ['*'], 'page', $page);
        $notesDibeli = $paginatedNotesDibeli->getCollection()->map(fn($tx) => $formatNote($tx->note));

        // Notes dibeli oleh user (hanya transaksi yang success dan note diterima) dengan filtering
        $favoriteNotes = $user->savedNotes()
            ->whereHas('note.noteStatus', fn($q) => $q->where('status', 'diterima'))
            ->with(['note.seller', 'note.noteTags.tag', 'note.reviews', 'note.likes', 'note.savedByUsers', 'note.transactions'])
            ->get()
            ->map(fn($saved) => $formatNote($saved->note));

        // Metadata pagination (opsional, bisa dihapus jika tidak perlu)
        $paginationMetaDijual = [
            'current_page' => $paginatedNotesDijual->currentPage(),
            'per_page' => $paginatedNotesDijual->perPage(),
            'total' => $paginatedNotesDijual->total(),
            'last_page' => $paginatedNotesDijual->lastPage(),
            'from' => $paginatedNotesDijual->firstItem(),
            'to' => $paginatedNotesDijual->lastItem(),
            'has_more_pages' => $paginatedNotesDijual->hasMorePages(),
            'path' => $paginatedNotesDijual->path(),
            'links' => [
                'first' => $paginatedNotesDijual->url(1),
                'last' => $paginatedNotesDijual->url($paginatedNotesDijual->lastPage()),
                'prev' => $paginatedNotesDijual->previousPageUrl(),
                'next' => $paginatedNotesDijual->nextPageUrl(),
            ]
        ];
        $paginationMetaDibeli = [
            'current_page' => $paginatedNotesDibeli->currentPage(),
            'per_page' => $paginatedNotesDibeli->perPage(),
            'total' => $paginatedNotesDibeli->total(),
            'last_page' => $paginatedNotesDibeli->lastPage(),
            'from' => $paginatedNotesDibeli->firstItem(),
            'to' => $paginatedNotesDibeli->lastItem(),
            'has_more_pages' => $paginatedNotesDibeli->hasMorePages(),
            'path' => $paginatedNotesDibeli->path(),
            'links' => [
                'first' => $paginatedNotesDibeli->url(1),
                'last' => $paginatedNotesDibeli->url($paginatedNotesDibeli->lastPage()),
                'prev' => $paginatedNotesDibeli->previousPageUrl(),
                'next' => $paginatedNotesDibeli->nextPageUrl(),
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Catatan profil pengguna',
            'data' => [
                'notes_dijual' => $notesDijual,
                'notes_dibeli' => $notesDibeli,
                'favorite' => $favoriteNotes,
            ],
            'pagination' => [
                'notes_dijual' => $paginationMetaDijual,
                'notes_dibeli' => $paginationMetaDibeli,
            ],
        ]);
    }

    // public function updatePhoto(Request $request) {}

    // public function changePassword(Request $request) {}


}
