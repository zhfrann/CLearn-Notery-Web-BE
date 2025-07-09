<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Note;
use App\Models\NoteFile;
use App\Models\NoteTag;
use App\Models\Tag;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    public function getAllNotes(Request $request)
    {
        $notes = Note::with([
            'course.major.faculty',
            'course.semester',
            'noteTags.tag',
            'files'
        ])->get();

        $result = $notes->map(function ($note) {
            $course = $note->course;
            $major = $course->major ?? null;
            $faculty = $major ? $major->faculty : null;
            $semester = $course->semester ?? null;

            return [
                'note_id' => $note->note_id,
                'judul' => $note->judul,
                'deskripsi' => $note->deskripsi,
                'harga' => $note->harga,
                'jumlah_like' => $note->jumlah_like,
                'jumlah_favorit' => $note->savedByUsers->count(),
                'jumlah_dikunjungi' => $note->jumlah_dikunjungi,
                'gambar_preview' => $note->gambar_preview ? url($note->gambar_preview) : asset('images/default_preview.png'),
                'fakultas' => $faculty ? [
                    'id' => $faculty->faculty_id,
                    'nama' => $faculty->nama_fakultas,
                ] : null,
                'prodi' => $major ? [
                    'id' => $major->major_id,
                    'nama' => $major->nama_jurusan,
                ] : null,
                'semester' => $semester ? [
                    'id' => $semester->semester_id,
                    'nama' => $semester->nomor_semester,
                ] : null,
                'matkul_favorit' => $course ? [
                    'id' => $course->course_id,
                    'nama' => $course->nama_mk,
                ] : null,
                'tags' => $note->noteTags->map(function ($nt) {
                    return $nt->tag->nama_tag ?? null;
                })->filter()->values(),
                'files' => $note->files->map(function ($file) {
                    return [
                        'nama_file' => $file->nama_file,
                        'path_file' => url(asset('storage/' . $file->path_file)),
                        'created_at' => $file->created_at->toIso8601String()
                    ];
                }),
                'created_at' => $note->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil semua note',
            'data' => $result
        ]);
    }

    public function latestNotes(Request $request) {}

    public function mostLikeNotes(Request $request) {}

    public function topCreator(Request $request) {}

    public function createNote(Request $request)
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'harga' => ['required', 'integer', 'min:0'],
            'tag_id' => ['required', 'array'],
            'tag_id.*' => ['exists:tags,tag_id'],
            'fakultas_id' => ['required', 'exists:faculties,faculty_id'],
            'prodi_id' => ['required', 'exists:majors,major_id'],
            'semester_id' => ['required', 'exists:semesters,semester_id'],
            'matkul_id' => ['required', 'exists:courses,course_id'],
            'files' => ['required', 'array'],
            'files.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:20480']
        ]);

        if (!$request->hasFile('files')) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Pastikan mengupload minimal 1 file.'
            ], 422));
        }

        // 1. Simpan note
        $note = Note::query()->create([
            'seller_id' => $request->user()->user_id,
            'course_id' => $validated['matkul_id'],
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'harga' => $validated['harga'],
            'jumlah_like' => 0,
            'jumlah_dikunjungi' => 0,
        ]);

        // 2. Simpan tags (NoteTag)
        foreach ($validated['tag_id'] as $tagId) {
            NoteTag::query()->create([
                'note_id' => $note->note_id,
                'tag_id' => $tagId,
            ]);
        }

        // 3. Simpan files (NoteFile)
        $filesData = [];
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            if (!is_array($files)) {
                $files = [$files];
            }
            foreach ($files as $file) {
                $filename = uniqid() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('notes/files', $filename, 'public');
                $noteFile = \App\Models\NoteFile::create([
                    'note_id' => $note->note_id,
                    'nama_file' => $file->getClientOriginalName(),
                    'path_file' => $path,
                    'tipe' => $file->getClientOriginalExtension(),
                ]);
                $filesData[] = [
                    'nama_file' => $noteFile->nama_file,
                    'path_file' => url(asset('storage/' . $noteFile->path_file)),
                    'created_at' => $noteFile->created_at->toIso8601String()
                ];
            }
        }

        // Setelah proses upload file
        $gambarPreview = null;
        foreach ($filesData as $file) {
            if (preg_match('/\.(jpg|jpeg|png)$/i', $file['nama_file'])) {
                $gambarPreview = $file['path_file'];
                break;
            }
        }
        if (!$gambarPreview) {
            // Set gambar default jika tidak ada gambar
            $gambarPreview = asset('images/default_preview.png');
        }

        // Update kolom gambar_preview di tabel notes
        $note->gambar_preview = $gambarPreview;
        $note->save();

        // 4. Ambil data relasi untuk response
        $course = Course::query()->with(['major.faculty', 'semester'])->find($validated['matkul_id']);
        $major = $course->major;
        $faculty = $major->faculty;
        $semester = $course->semester;

        // 5. Ambil nama tags
        $tags = Tag::whereIn('tag_id', $validated['tag_id'])->pluck('nama_tag')->toArray();

        // 6. Response
        return response()->json([
            'success' => true,
            'message' => 'Berhasil membuat note',
            'data' => [
                'note_id' => $note->note_id,
                'judul' => $note->judul,
                'deskripsi' => $note->deskripsi,
                'harga' => $note->harga,
                'gambar_preview' => $note->gambar_preview ? url($note->gambar_preview) : asset('images/default_preview.png'),
                'fakultas' => [
                    'id' => $faculty->faculty_id,
                    'nama' => $faculty->nama_fakultas,
                ],
                'prodi' => [
                    'id' => $major->major_id,
                    'nama' => $major->nama_jurusan,
                ],
                'semester' => [
                    'id' => $semester->semester_id,
                    'nama' => $semester->nomor_semester,
                ],
                'matkul_favorit' => [
                    'id' => $course->course_id,
                    'nama' => $course->nama_mk,
                ],
                'tags' => $tags,
                'files' => $filesData,
                'created_at' => $note->created_at->toIso8601String(),
            ]
        ]);
    }

    public function getNoteDetail(Request $request, string $id) {}

    public function addLikeNote(Request $request, string $id) {}

    public function addFavoriteNote(Request $request, string $id) {}

    public function updateNote(Request $request, string $id) {}

    public function deleteNote(Request $request, string $id) {}

    // public function buyNote(Request $request, string $id) {}
}
