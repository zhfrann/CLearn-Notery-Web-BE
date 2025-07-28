<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Note;
use App\Models\NoteFile;
use App\Models\NoteStatus;
use App\Models\NoteTag;
use App\Models\Tag;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans;


class NoteController extends Controller
{
    public function getAllNotes(Request $request)
    {
        $user = $request->user();
        $notes = Note::with([
            'course.major.faculty',
            'course.semester',
            'noteTags.tag',
            'files',
            'likes',
            'savedByUsers',
            'transactions',
            'reviews',
        ])->get();

        $result = $notes->map(function ($note) use ($user) {
            $course = $note->course;
            $major = $course->major ?? null;
            $faculty = $major ? $major->faculty : null;
            $semester = $course->semester ?? null;

            return [
                'note_id' => $note->note_id,
                'seller' => [
                    'seller_id' => $note->seller_id,
                    'name' => $note->seller->name,
                    'username' => $note->seller->username,
                    'foto_profil' => url($note->seller->foto_profil_url),
                    'isTopCreator' => null,
                ],
                'judul' => $note->judul,
                'deskripsi' => $note->deskripsi,
                'harga' => $note->harga,
                'status' => $note->noteStatus->status,
                'jumlah_like' => $note->likes()->count(),
                'jumlah_favorit' => $note->savedByUsers->count(),
                'jumlah_dikunjungi' => $note->jumlah_dikunjungi,
                'jumlah_terjual' => $note->transactions->where('status', 'success')->count(),
                'gambar_preview' => url(asset('storage/' . $note->gambar_preview)),
                'rating' => round($note->reviews->avg('rating') ?? 0, 2),
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
                'isLiked' => $user ? $note->likes->contains('user_id', $user->user_id) : false,
                'isFavorite' => $user ? $note->savedByUsers->contains('user_id', $user->user_id) : false,
                'created_at' => $note->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil semua note',
            'data' => $result
        ]);
    }

    public function latestNotes(Request $request)
    {
        $user = $request->user();
        $notes = Note::approved()
            ->with(['noteTags.tag', 'savedByUsers', 'reviews', 'likes', 'savedByUsers', 'transactions'])
            ->withCount(['transactions as jumlah_terjual' => function ($query) {
                $query->where('status', 'success');
            }])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($note) use ($user) {
                return [
                    'note_id' => $note->note_id,
                    'seller' => [
                        'seller_id' => $note->seller_id,
                        'name' => $note->seller->name,
                        'username' => $note->seller->username,
                        'foto_profil' => url($note->seller->foto_profil_url),
                        'isTopCreator' => null,
                    ],
                    'judul' => $note->judul,
                    'deskripsi' => $note->deskripsi,
                    'harga' => $note->harga,
                    'jumlah_like' => $note->likes()->count(),
                    'jumlah_favorit' => $note->savedByUsers->count(),
                    'jumlah_dikunjungi' => $note->jumlah_dikunjungi,
                    'jumlah_terjual' => $note->jumlah_terjual,
                    'rating' => round($note->reviews->avg('rating') ?? 0, 2),
                    'gambar_preview' => url(asset('storage/' . $note->gambar_preview)),
                    'tags' => $note->noteTags->map(function ($noteTag) {
                        return $noteTag->tag->nama_tag ?? null;
                    })->filter()->values(),
                    'isLiked' => $user ? $note->likes->contains('user_id', $user->user_id) : false,
                    'isFavorite' => $user ? $note->savedByUsers->contains('user_id', $user->user_id) : false,
                    'created_at' => $note->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Note terbaru',
            'data' => $notes,
        ]);
    }

    public function mostLikeNotes(Request $request)
    {
        $user = $request->user();
        $notes = Note::approved()
            ->with(['noteTags.tag', 'savedByUsers', 'reviews', 'likes', 'transactions'])
            ->withCount(['transactions as jumlah_terjual' => function ($query) {
                $query->where('status', 'success');
            }])
            ->orderByDesc('jumlah_like')
            ->get()
            ->map(function ($note) use ($user) {
                return [
                    'note_id' => $note->note_id,
                    'seller' => [
                        'seller_id' => $note->seller_id,
                        'name' => $note->seller->name,
                        'username' => $note->seller->username,
                        'foto_profil' => url($note->seller->foto_profil_url),
                        'isTopCreator' => null,
                    ],
                    'judul' => $note->judul,
                    'deskripsi' => $note->deskripsi,
                    'harga' => $note->harga,
                    'jumlah_like' => $note->likes()->count(),
                    'jumlah_favorit' => $note->savedByUsers->count(),
                    'jumlah_dikunjungi' => $note->jumlah_dikunjungi,
                    'jumlah_terjual' => $note->jumlah_terjual,
                    'rating' => round($note->reviews->avg('rating') ?? 0, 2),
                    'gambar_preview' => url(asset('storage/' . $note->gambar_preview)),
                    'tags' => $note->noteTags->map(function ($noteTag) {
                        return $noteTag->tag->nama_tag ?? null;
                    })->filter()->values(),
                    'isLiked' => $user ? $note->likes->contains('user_id', $user->user_id) : false,
                    'isFavorite' => $user ? $note->savedByUsers->contains('user_id', $user->user_id) : false,
                    'created_at' => $note->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Note paling banyak disukai',
            'data' => $notes,
        ]);
    }

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
            'files.*' => ['file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,jpg,jpeg,png', 'max:51200']
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

        $noteStatus = NoteStatus::query()->create([
            'note_id' => $note->note_id,
            'status' => 'menunggu',
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
                    'path_file' => $noteFile->path_file,
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
            $gambarPreview = 'images/default_preview.png';
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
                'seller' => [
                    'seller_id' => $note->seller_id,
                    'name' => $note->seller->name,
                    'username' => $note->seller->username,
                    'foto_profil' => url($note->seller->foto_profil_url),
                    'isTopCreator' => null,
                ],
                'judul' => $note->judul,
                'deskripsi' => $note->deskripsi,
                'harga' => $note->harga,
                'status' => $noteStatus->status,
                'gambar_preview' => url(asset('storage/' . $note->gambar_preview)),
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
                // 'files' => $filesData,
                'files' => collect($filesData)->map(function ($file) {
                    return [
                        'nama_file' => $file['nama_file'],
                        'path_file' => url('storage/' . $file['path_file']),
                        'created_at' => $file['created_at']
                    ];
                }),
                'created_at' => $note->created_at->toIso8601String(),
            ]
        ]);
    }

    public function getNoteDetail(Request $request, string $id)
    {
        try {
            $user = $request->user();

            $note = Note::with(['noteTags.tag', 'seller', 'likes', 'savedByUsers', 'transactions'])
                ->withCount(['savedByUsers', 'transactions'])
                ->withAvg('reviews', 'rating')
                ->findOrFail($id);

            // Tambahkan kunjungan
            $note->increment('jumlah_dikunjungi', 1);

            return response()->json([
                'success' => true,
                'message' => 'Detail note',
                'data' => [
                    'note_id' => $note->note_id,
                    'seller' => [
                        'seller_id' => $note->seller->user_id,
                        'name' => $note->seller->nama,
                        'username' => $note->seller->username,
                        'foto_profil' => url($note->seller->foto_profil_url),
                        'isTopCreator' => null, //TODO Implement isTopCreator logic
                    ],
                    'judul' => $note->judul,
                    'deskripsi' => $note->deskripsi,
                    'harga' => $note->harga,
                    'jumlah_like' => $note->likes()->count(),
                    'jumlah_favorit' => $note->saved_by_users_count,
                    'jumlah_dikunjungi' => $note->jumlah_dikunjungi,
                    'jumlah_terjual' => $note->transactions_count,
                    'rating' => round($note->reviews_avg_rating ?? 0, 2),
                    'gambar_preview' => url(asset('storage/' . $note->gambar_preview)),
                    'tags' => $note->noteTags->pluck('tag.nama_tag'),
                    'isLiked' => $user ? $note->likes->contains('user_id', $user->user_id) : false,
                    'isFavorite' => $user ? $note->savedByUsers->contains('user_id', $user->user_id) : false,
                    'created_at' => $note->created_at->toIso8601String(),
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan',
            ], 404);
        }
    }

    public function likeNote(Request $request, string $id)
    {
        $user = auth()->user();
        try {
            $note = Note::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan.',
                'data' => null
            ], 404);
        }

        // Cek apakah user sudah like note ini
        $alreadyLiked = $note->likes()->where('user_id', $user->user_id)->exists();
        if ($alreadyLiked) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu sudah like note ini.',
                'data' => [
                    'note_id' => $note->note_id,
                    'judul' => $note->judul,
                    'total_like' => $note->likes()->count(),
                ]
            ], 200);
        }

        // Simpan like
        $note->likes()->create([
            'user_id' => $user->user_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambah like note',
            'data' => [
                'note_id' => $note->note_id,
                'judul' => $note->judul,
                'total_like' => $note->likes()->count(),
            ]
        ], 201);
    }

    public function unlikeNote(Request $request, string $id)
    {
        $user = auth()->user();
        try {
            $note = Note::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan.',
                'data' => null
            ], 404);
        }

        $like = $note->likes()->where('user_id', $user->user_id)->first();

        if (!$like) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu belum like note ini.',
                'data' => [
                    'note_id' => $note->note_id,
                    'judul' => $note->judul,
                    'total_like' => $note->likes()->count(),
                ]
            ], 200);
        }

        $like->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus like note',
            'data' => [
                'note_id' => $note->note_id,
                'judul' => $note->judul,
                'total_like' => $note->likes()->count(),
            ]
        ], 200);
    }

    public function addFavoriteNote(Request $request, string $id)
    {
        $user = auth()->user();
        try {
            $note = Note::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan.',
                'data' => null
            ], 404);
        }

        // Cek apakah sudah favorit
        $alreadyFavorited = $note->savedByUsers()->where('user_id', $user->user_id)->exists();
        if ($alreadyFavorited) {
            return response()->json([
                'success' => false,
                'message' => 'Note sudah ada di favorit.',
                'data' => [
                    'note_id' => $note->note_id,
                    'judul' => $note->judul,
                    'total_favorite' => $note->savedByUsers()->count(),
                ]
            ], 200);
        }

        $note->savedByUsers()->create([
            'user_id' => $user->user_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambah favorit note',
            'data' => [
                'note_id' => $note->note_id,
                'judul' => $note->judul,
                'total_favorite' => $note->savedByUsers()->count(),
            ]
        ], 201);
    }

    public function removeFavoriteNote(Request $request, string $id)
    {
        $user = auth()->user();
        try {
            $note = Note::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan.',
                'data' => null
            ], 404);
        }

        $favorite = $note->savedByUsers()->where('user_id', $user->user_id)->first();

        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Note belum ada di favorit.',
                'data' => [
                    'note_id' => $note->note_id,
                    'judul' => $note->judul,
                    'total_favorite' => $note->savedByUsers()->count(),
                ]
            ], 200);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus favorit note',
            'data' => [
                'note_id' => $note->note_id,
                'judul' => $note->judul,
                'total_favorite' => $note->savedByUsers()->count(),
            ]
        ], 200);
    }

    public function getFiles(Request $request, string $id)
    {
        $user = $request->user();

        try {
            $note = Note::query()->with('files')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan',
                'data' => null
            ], 404);
        }

        $isOwner = $note->seller_id === $user->user_id;

        $hasPurchased = Transaction::where('note_id', $note->note_id)
            ->where('buyer_id', $user->user_id)
            ->where('status', 'paid')
            ->exists();

        // User hanya bisa akses files jika sudah membeli atau adalah pemilik
        if (!$hasPurchased && !$isOwner) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum membeli note ini',
                'data' => null
            ], 403);
        }

        $files = $note->files->map(function ($file) {
            return [
                'note_file_id' => $file->note_file_id,
                'nama_file' => $file->nama_file,
                'path_file' => url('storage/' . $file->path_file),
                'tipe' => $file->tipe,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar files note',
            'data' => [
                'note_id' => $note->note_id,
                'judul' => $note->judul,
                'files' => $files,
            ]
        ]);
    }

    public function updateNote(Request $request, string $id)
    {
        $user = $request->user();

        try {
            $note = Note::with(['seller', 'noteStatus', 'course.major.faculty', 'course.semester', 'noteTags.tag', 'files'])
                ->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan',
                'data' => null
            ], 404);
        }

        // Cek apakah user adalah pemilik note
        if ($note->seller_id !== $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengedit note ini',
                'data' => null
            ], 403);
        }

        // Validasi input
        $request->validate([
            'judul' => 'sometimes|string|max:255',
            'deskripsi' => 'sometimes|string',
            'files.*' => 'sometimes|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,rar,jpg,jpeg,png|max:10240'
        ]);

        // Update note fields jika ada di request
        $updateData = [];
        if ($request->has('judul')) {
            $updateData['judul'] = $request->judul;
        }
        if ($request->has('deskripsi')) {
            $updateData['deskripsi'] = $request->deskripsi;
        }

        if (!empty($updateData)) {
            $note->update($updateData);
        }

        // Handle file uploads jika ada
        if ($request->hasFile('files')) {
            // Hapus file lama (opsional, atau bisa keep untuk backup)
            foreach ($note->files as $oldFile) {
                if (Storage::disk('public')->exists($oldFile->path_file)) {
                    Storage::disk('public')->delete($oldFile->path_file);
                }
                $oldFile->delete();
            }

            // Upload file baru
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $filename = time() . '_' . $originalName;
                $path = $file->storeAs('note/files', $filename, 'public');

                // Simpan ke database
                NoteFile::create([
                    'note_id' => $note->note_id,
                    'nama_file' => $originalName,
                    'path_file' => $path,
                    'tipe' => $file->getClientOriginalExtension()
                ]);
            }

            // Update gambar preview jika ada file gambar
            $firstImage = null;
            foreach ($request->file('files') as $file) {
                if (in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
                    $firstImage = time() . '_' . $file->getClientOriginalName();
                    break;
                }
            }
            if ($firstImage) {
                $note->update(['gambar_preview' => 'notes/files/' . $firstImage]);
            }

            // Refresh note dengan files terbaru
            // Refresh note dengan files terbaru
            $note->refresh();
            $note->load(['seller', 'noteStatus', 'course.major.faculty', 'course.semester', 'noteTags.tag', 'files']);
        }

        // Format response
        $responseData = [
            'note_id' => $note->note_id,
            'seller' => [
                'seller_id' => $note->seller->user_id,
                'nama' => $note->seller->nama,
                'username' => $note->seller->username,
                'foto_profil' => $note->seller->foto_profil ?
                    url('storage/' . $note->seller->foto_profil) : null,
            ],
            'judul' => $note->judul,
            'deskripsi' => $note->deskripsi,
            'harga' => $note->harga,
            'status' => $note->noteStatus ? $note->noteStatus->status : null,
            'gambar_preview' => $note->gambar_preview ?
                url('storage/' . $note->gambar_preview) : null,
            'fakultas' => [
                'faculty_id' => $note->course->major->faculty->faculty_id,
                'nama_fakultas' => $note->course->major->faculty->nama_fakultas,
            ],
            'prodi' => [
                'major_id' => $note->course->major->major_id,
                'nama_program_studi' => $note->course->major->nama_jurusan,
            ],
            'semester' => [
                'semester_id' => $note->course->semester->semester_id,
                'nama_semester' => $note->course->semester->nomor_semester,
            ],
            'matkul_favorit' => [
                'course_id' => $note->course->course_id,
                'nama_matkul' => $note->course->nama_mk,
            ],
            'tags' => $note->noteTags->pluck('tag.nama_tag'),
            'files' => $note->files->map(function ($file) {
                return [
                    'nama_file' => $file->nama_file,
                    'path_file' => url('storage/' . $file->path_file),
                    'created_at' => $file->created_at->toIso8601String(),
                    'updated_at' => $file->updated_at->toIso8601String(),
                ];
            }),
            'created_at' => $note->created_at->toIso8601String(),
            'updated_at' => $note->updated_at->toIso8601String(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengedit note',
            'data' => $responseData
        ]);
    }

    public function deleteNote(Request $request, string $id)
    {
        $user = $request->user();

        try {
            $note = Note::with(['files'])->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan',
                'data' => null
            ], 404);
        }

        // Cek apakah user adalah pemilik note
        if ($note->seller_id !== $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghapus note ini',
                'data' => null
            ], 403);
        }

        // Cek apakah note sudah pernah dibeli (ada transaksi)
        $hasPurchases = Transaction::where('note_id', $note->note_id)->exists();

        if ($hasPurchases) {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak dapat dihapus karena sudah pernah dibeli',
                'data' => null
            ], 400);
        }

        // Simpan data untuk response sebelum dihapus
        $responseData = [
            'note_id' => $note->note_id,
            'judul' => $note->judul,
        ];

        // Hapus file-file terkait dari storage
        foreach ($note->files as $file) {
            if (Storage::disk('public')->exists($file->path_file)) {
                Storage::disk('public')->delete($file->path_file);
            }
        }

        // Hapus gambar preview jika ada
        if ($note->gambar_preview && Storage::disk('public')->exists($note->gambar_preview)) {
            Storage::disk('public')->delete($note->gambar_preview);
        }

        // $note->files()->delete();
        // $note->noteTags()->delete();
        // $note->likes()->delete();
        // $note->savedByUsers()->delete();
        // $note->reviews()->delete();
        // $note->noteStatus()->delete();

        // Hapus note
        $note->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus note',
            'data' => $responseData
        ]);
    }

    public function buyNote(Request $request, string $id)
    {
        $user = $request->user();

        // Validasi input
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png|max:10240'
        ]);

        try {
            $note = Note::with('seller')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan',
                'data' => null
            ], 404);
        }

        // Cek apakah user mencoba membeli note miliknya sendiri
        if ($note->seller_id === $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat membeli note milik sendiri',
                'data' => null
            ], 400);
        }

        // Cek apakah user sudah pernah membeli note ini
        $existingTransaction = Transaction::where('note_id', $note->note_id)
            ->where('buyer_id', $user->user_id)
            ->first();

        if ($existingTransaction) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah pernah membeli note ini',
                'data' => null
            ], 400);
        }

        // Upload bukti pembayaran
        $file = $request->file('bukti_pembayaran');
        $filename = $user->username . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('bukti_pembayaran', $filename, 'public');

        // Buat transaksi baru
        $transaction = Transaction::create([
            'note_id' => $note->note_id,
            'buyer_id' => $user->user_id,
            'status' => 'paid',
            // 'status' => 'menunggu', // Status awal pending, akan diubah admin/seller
            'tgl_transaksi' => now(),
            // 'catatan' => 'Transaksi pembelian note: ' . $note->judul,
            'bukti_pembayaran' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil membeli note',
            'data' => [
                'transaction_id' => $transaction->transaction_id,
                'note_id' => $note->note_id,
                'user_id' => $user->user_id,
                'username' => $user->username,
                'nama' => $user->nama,
                'judul' => $note->judul,
                'status' => $transaction->status,
                'bukti_pembayaran' => url('storage/' . $path),
            ]
        ]);
    }

    public function buyNoteMidtrans(Request $request, string $id)
    {
        $buyer = $request->user();

        try {
            $note = Note::with(['seller', 'course'])
                ->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan',
                'data' => null
            ], 404);
        }

        // Validasi buyer tidak bisa beli note sendiri
        if ($note->seller_id === $buyer->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat membeli note sendiri',
                'data' => null
            ], 400);
        }

        // Validasi hanya note dengan status 'diterima' yang bisa dibeli
        if ($note->noteStatus->status !== 'diterima') {
            return response()->json([
                'success' => false,
                'message' => 'Status note ini tidak bisa dibeli',
                'data' => null
            ], 400);
        }

        // Cek apakah user sudah pernah membeli note ini dengan status 'paid'
        $paidTransaction = Transaction::where('note_id', $note->note_id)
            ->where('buyer_id', $buyer->user_id)
            ->where('status', 'paid')
            ->first();

        if ($paidTransaction) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah membeli note ini',
                'data' => null
            ], 400);
        }

        // Cek apakah ada transaksi pending yang bisa diupdate
        $pendingTransaction = Transaction::where('note_id', $note->note_id)
            ->where('buyer_id', $buyer->user_id)
            ->where('status', 'pending')
            ->first();

        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        // Generate unique order ID
        $orderId = 'NOTE-' . $note->note_id . '-' . $buyer->user_id . '-' . time();

        // Calculate all fees
        $notePrice = $note->harga;
        $platformFeeRate = 0.10; // 10%
        $midtransFeeRate = 0.029; // 2.9%
        $midtransFixedFee = 2000; // Rp 2,000

        // Calculate fees
        $platformFee = round($notePrice * $platformFeeRate);
        $midtransFee = round(($notePrice * $midtransFeeRate) + $midtransFixedFee);

        // Total yang dibayar buyer
        $grossAmount = $notePrice + $platformFee + $midtransFee;

        // Amount yang masuk ke seller (note price aja)
        $sellerAmount = $notePrice;

        // Prepare Midtrans parameters
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $buyer->nama ?? $buyer->username,
                'last_name' => '',
                'email' => $buyer->email,
            ],
            'item_details' => [
                [
                    'id' => $note->note_id,
                    'price' => $notePrice,
                    'quantity' => 1,
                    'name' => $note->judul,
                    'category' => 'Digital Product',
                ],
                [
                    'id' => 'platform_fee',
                    'price' => $platformFee,
                    'quantity' => 1,
                    'name' => 'Platform Fee (10%)',
                    'category' => 'Service Fee',
                ],
                [
                    'id' => 'payment_fee',
                    'price' => $midtransFee,
                    'quantity' => 1,
                    'name' => 'Payment Processing Fee',
                    'category' => 'Service Fee',
                ]
            ]
        ];

        try {
            // Generate Snap Token
            $snapToken = Snap::getSnapToken($params);

            // Save or update transaction to database
            if ($pendingTransaction) {
                // Update existing pending transaction
                $pendingTransaction->update([
                    'midtrans_order_id' => $orderId,
                    'snap_token' => $snapToken,
                    'jumlah' => $grossAmount,
                    'platform_fee' => $platformFee,
                    'seller_amount' => $sellerAmount,
                    'tgl_transaksi' => now(),
                ]);
                $transaction = $pendingTransaction;
            } else {
                // Save transaction to database
                $transaction = Transaction::create([
                    'buyer_id' => $buyer->user_id,
                    'note_id' => $note->note_id,
                    'midtrans_order_id' => $orderId,
                    'snap_token' => $snapToken,
                    'jumlah' => $grossAmount,           // Total yang dibayar buyer
                    'platform_fee' => $platformFee,    // Fee untuk platform
                    'seller_amount' => $sellerAmount,  // Amount untuk seller
                    'status' => 'pending',
                    'tgl_transaksi' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil membuat transaksi',
                'data' => [
                    'transaction_id' => $transaction->transaction_id,
                    'order_id' => $orderId,
                    'snap_token' => $snapToken,
                    'breakdown' => [
                        'note_price' => $notePrice,
                        'platform_fee' => $platformFee,
                        'payment_fee' => $midtransFee,
                        'total_amount' => $grossAmount,
                        'seller_receives' => $sellerAmount,
                    ],
                    'note' => [
                        'note_id' => $note->note_id,
                        'judul' => $note->judul,
                        'harga' => $note->harga,
                        'seller' => [
                            'seller_id' => $note->seller->user_id,
                            'nama' => $note->seller->nama,
                            'username' => $note->seller->username,
                        ]
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    // POST /api/payment/manual-update
    public function manualUpdatePayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'transaction_status' => 'required|string',
            'transaction_id' => 'required|string',
            'payment_type' => 'required|string',
        ]);

        $transaction = Transaction::where('midtrans_order_id', $request->order_id)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Update transaction
        $transaction->update([
            'midtrans_transaction_id' => $request->transaction_id,
            'payment_method' => $request->payment_type,
            'status' => $request->transaction_status === 'settlement' ? 'paid' : 'failed'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully'
        ]);
    }
}
