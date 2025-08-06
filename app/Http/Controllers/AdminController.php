<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Report;
use App\Models\User;
use App\Models\UserAction;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function getWithdrawRequests(Request $request)
    {
        $withdrawRequests = WithdrawRequest::with('user')
            // ->where('status', 'menunggu')
            ->orderBy('tgl_request', 'asc')
            ->get()
            ->map(function ($data) {
                return [
                    'withdraw_request_id' => $data->withdraw_request_id,
                    'user_id' => $data->user_id,
                    'username' => $data->user->username ?? null,
                    'jumlah' => $data->jumlah,
                    'status' => $data->status,
                    'tgl_request' => $data->tgl_request,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Daftar pengajuan withdraw menunggu',
            'data' => $withdrawRequests
        ]);
    }

    public function accWithdrawRequests(Request $request, string $id)
    {
        $withdraw = WithdrawRequest::find($id);

        if (!$withdraw || $withdraw->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Withdraw request tidak ditemukan atau sudah diproses.',
            ], 404);
        }

        $withdraw->status = 'diterima_admin';
        // $withdraw->tgl_transfer = now(); // jika ada kolom tgl_transfer
        $withdraw->save();

        return response()->json([
            'success' => true,
            'message' => 'Withdraw request berhasil diupdate.',
            'data' => [
                'withdraw_request_id' => $withdraw->withdraw_request_id,
                'user_id' => $withdraw->user_id,
                'username' => $withdraw->user->username ?? null,
                'jumlah' => $withdraw->jumlah,
                'status' => $withdraw->status,
                'tgl_request' => $withdraw->tgl_request,
            ]
        ]);
    }

    // GET /api/admin/reports - Get all reports for admin
    public function getAllReports(Request $request)
    {
        // Validasi query parameters untuk pagination
        $validated = $request->validate([
            'size' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        $size = $validated['size'] ?? 15;
        $page = $validated['page'] ?? 1;

        // Group laporan berdasarkan reported_user_id dan report_type_value
        $query = Report::with(['reportedUser', 'reportType'])
            ->select('reported_user_id', 'report_type_value', DB::raw('COUNT(*) as jumlah_laporan'), 'created_at')
            ->groupBy('reported_user_id', 'report_type_value', 'created_at')
            ->orderByDesc('jumlah_laporan');

        $paginated = $query->paginate($size, ['*'], 'page', $page);

        $result = $paginated->getCollection()->map(function ($row) {
            $reportedUser = $row->reportedUser;
            $reportType = $row->reportType;
            $title = ($reportType ? $reportType->label : $row->report_type_value) . ': Terlapor dengan ' . ($reportedUser ? $reportedUser->username : '-');
            return [
                'title' => $title,
                'jumlah_laporan' => $row->jumlah_laporan,
                'created_at' => $row->created_at->toIso8601String()
            ];
        });

        // Metadata pagination (opsional)
        $paginationMeta = [
            'current_page' => $paginated->currentPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'last_page' => $paginated->lastPage(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem(),
            'has_more_pages' => $paginated->hasMorePages(),
            'path' => $paginated->path(),
            'links' => [
                'first' => $paginated->url(1),
                'last' => $paginated->url($paginated->lastPage()),
                'prev' => $paginated->previousPageUrl(),
                'next' => $paginated->nextPageUrl(),
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Daftar semua laporan',
            'data' => $result,
            'pagination' => $paginationMeta,
        ]);
    }

    public function getAllNotesSubmission(Request $request)
    {
        // Validasi query parameters untuk pagination
        $validated = $request->validate([
            'size' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        $size = $validated['size'] ?? 20;
        $page = $validated['page'] ?? 1;

        // Query notes yang statusnya 'menunggu'
        $notesQuery = Note::query()
            ->join('note_statuses', 'notes.note_id', '=', 'note_statuses.note_id')
            ->whereHas('noteStatus', function ($q) {
                $q->whereIn('status', ['menunggu', 'diproses', 'diterima', 'ditolak']);
            })
            ->with([
                'seller:user_id,nama,username,foto_profil',
                'noteStatus',
            ])
            ->orderByRaw("CASE
                WHEN note_statuses.status = 'menunggu' THEN 0
                WHEN note_statuses.status = 'diproses' THEN 1
                WHEN note_statuses.status = 'diterima' THEN 2
                WHEN note_statuses.status = 'ditolak' THEN 3
                ELSE 4 END")
            ->orderBy('notes.created_at', 'asc');

        $paginatedNotes = $notesQuery->paginate($size, ['*'], 'page', $page);

        $result = $paginatedNotes->getCollection()->map(function ($note) {
            return [
                'note_id' => $note->note_id,
                'title' => $note->judul,
                'seller' => [
                    'seller_id' => $note->seller->user_id,
                    'nama' => $note->seller->nama,
                    'username' => $note->seller->username,
                    'foto_profil' => $note->seller->foto_profil ? url('storage/' . $note->seller->foto_profil) : null,
                ],
                'status' => $note->noteStatus ? $note->noteStatus->status : null,
                'created_at' => $note->created_at->toIso8601String(),
            ];
        });

        // Metadata pagination
        $paginationMeta = [
            'current_page' => $paginatedNotes->currentPage(),
            'per_page' => $paginatedNotes->perPage(),
            'total' => $paginatedNotes->total(),
            'last_page' => $paginatedNotes->lastPage(),
            'from' => $paginatedNotes->firstItem(),
            'to' => $paginatedNotes->lastItem(),
            'has_more_pages' => $paginatedNotes->hasMorePages(),
            'path' => $paginatedNotes->path(),
            'links' => [
                'first' => $paginatedNotes->url(1),
                'last' => $paginatedNotes->url($paginatedNotes->lastPage()),
                'prev' => $paginatedNotes->previousPageUrl(),
                'next' => $paginatedNotes->nextPageUrl(),
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Daftar notes yang diajukan untuk dijual',
            'data' => $result,
            'pagination' => $paginationMeta,
        ]);
    }

    public function getAllHandledSubmission(Request $request)
    {
        // Validasi query parameters untuk pagination
        $validated = $request->validate([
            'size' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        $size = $validated['size'] ?? 20;
        $page = $validated['page'] ?? 1;

        // Query notes yang statusnya 'menunggu'
        $notesQuery = Note::query()->whereHas('noteStatus', function ($q) {
            // $q->whereIn('status', ['diterima', 'ditolak']);
            $q->whereIn('status', ['diterima']);
        })
            ->with([
                'seller:user_id,nama,username,foto_profil',
                'noteStatus',
            ])
            ->orderBy('updated_at', 'desc');

        $paginatedNotes = $notesQuery->paginate($size, ['*'], 'page', $page);

        $result = $paginatedNotes->getCollection()->map(function ($note) {
            return [
                'note_id' => $note->note_id,
                'title' => $note->judul,
                'seller' => [
                    'seller_id' => $note->seller->user_id,
                    'nama' => $note->seller->nama,
                    'username' => $note->seller->username,
                    'foto_profil' => $note->seller->foto_profil ? url('storage/' . $note->seller->foto_profil) : null,
                ],
                'status' => $note->noteStatus ? $note->noteStatus->status : null,
                'created_at' => $note->created_at->toIso8601String(),
            ];
        });

        // Metadata pagination
        $paginationMeta = [
            'current_page' => $paginatedNotes->currentPage(),
            'per_page' => $paginatedNotes->perPage(),
            'total' => $paginatedNotes->total(),
            'last_page' => $paginatedNotes->lastPage(),
            'from' => $paginatedNotes->firstItem(),
            'to' => $paginatedNotes->lastItem(),
            'has_more_pages' => $paginatedNotes->hasMorePages(),
            'path' => $paginatedNotes->path(),
            'links' => [
                'first' => $paginatedNotes->url(1),
                'last' => $paginatedNotes->url($paginatedNotes->lastPage()),
                'prev' => $paginatedNotes->previousPageUrl(),
                'next' => $paginatedNotes->nextPageUrl(),
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Daftar notes yang diajukan untuk dijual',
            'data' => $result,
            'pagination' => $paginationMeta,
        ]);
    }

    public function addSubmissionsToQueue(Request $request, string $id)
    {
        // Validasi ID note
        $note = Note::with('noteStatus')->find($id);

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan.',
            ], 404);
        }

        // Pastikan status saat ini adalah 'menunggu'
        if (!$note->noteStatus || $note->noteStatus->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak dalam status menunggu.',
            ], 400);
        }

        // Update status menjadi 'diproses'
        $note->noteStatus->status = 'diproses';
        $note->noteStatus->save();

        return response()->json([
            'success' => true,
            'message' => 'Note berhasil dimasukkan ke antrian proses.',
            'data' => [
                'note_id' => $note->note_id,
                'status' => $note->noteStatus->status,
            ],
        ]);
    }

    public function getAllQueuSubmissions(Request $request)
    {
        // Validasi query parameters untuk pagination
        $validated = $request->validate([
            'size' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        $size = $validated['size'] ?? 20;
        $page = $validated['page'] ?? 1;

        // Query notes yang statusnya 'menunggu'
        $notesQuery = Note::query()->whereHas('noteStatus', function ($q) {
            $q->where('status', 'diproses');
        })
            ->with([
                'seller:user_id,nama,username,foto_profil',
                'noteTags.tag:tag_id,nama_tag',
                'noteStatus',
                'likes',
                'reviews',
            ])
            ->orderBy('created_at', 'asc');

        $paginatedNotes = $notesQuery->paginate($size, ['*'], 'page', $page);

        $result = $paginatedNotes->getCollection()->map(function ($note) {
            return [
                'note_id' => $note->note_id,
                'no_antrian' => $note->noteStatus->note_status_id,
                'title' => $note->judul,
                'deskripsi' => $note->deskripsi,
                'seller' => [
                    'seller_id' => $note->seller->user_id,
                    'nama' => $note->seller->nama,
                    'username' => $note->seller->username,
                    'foto_profil' => $note->seller->foto_profil ? url('storage/' . $note->seller->foto_profil) : null,
                ],
                'tags' => $note->noteTags->map(function ($noteTag) {
                    return $noteTag->tag->nama_tag ?? null;
                })->filter()->values(),
                // 'rating' => round($note->reviews->avg('rating') ?? 0, 2),
                'gambar_preview' => url(asset('storage/' . $note->gambar_preview)),
                'status' => $note->noteStatus ? $note->noteStatus->status : null,
                'created_at' => $note->created_at->toIso8601String(),
            ];
        });

        // Metadata pagination
        $paginationMeta = [
            'current_page' => $paginatedNotes->currentPage(),
            'per_page' => $paginatedNotes->perPage(),
            'total' => $paginatedNotes->total(),
            'last_page' => $paginatedNotes->lastPage(),
            'from' => $paginatedNotes->firstItem(),
            'to' => $paginatedNotes->lastItem(),
            'has_more_pages' => $paginatedNotes->hasMorePages(),
            'path' => $paginatedNotes->path(),
            'links' => [
                'first' => $paginatedNotes->url(1),
                'last' => $paginatedNotes->url($paginatedNotes->lastPage()),
                'prev' => $paginatedNotes->previousPageUrl(),
                'next' => $paginatedNotes->nextPageUrl(),
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Daftar notes yang diajukan untuk dijual',
            'data' => $result,
            'pagination' => $paginationMeta,
        ]);
    }

    public function getDetailQueuSubmissions(Request $request, string $id)
    {
        $note = Note::with([
            'seller:user_id,nama,username,foto_profil',
            'noteStatus',
            'noteTags.tag:tag_id,nama_tag',
            'files',
        ])->find($id);

        if (!$note || !$note->noteStatus || $note->noteStatus->status !== 'diproses') {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan atau tidak dalam antrian proses.',
            ], 404);
        }

        $data = [
            'note_id' => $note->note_id,
            'no_antrian' => $note->noteStatus->note_status_id,
            'seller' => [
                'seller_id' => $note->seller->user_id ?? null,
                'nama' => $note->seller->nama ?? null,
                'username' => $note->seller->username ?? null,
                'foto_profil' => $note->seller->foto_profil ? url('storage/' . $note->seller->foto_profil) : null,
            ],
            'judul' => $note->judul,
            'deskripsi' => $note->deskripsi,
            'gambar_preview' => $note->gambar_preview ? url('storage/' . $note->gambar_preview) : null,
            'tags' => $note->noteTags->map(function ($noteTag) {
                return $noteTag->tag ? [
                    'tag_id' => $noteTag->tag->tag_id,
                    'nama_tag' => $noteTag->tag->nama_tag,
                ] : null;
            })->filter()->values(),
            'files' => $note->files->map(function ($file) {
                return [
                    'note_file_id' => $file->note_file_id,
                    'nama_file' => $file->nama_file,
                    'path_file' => url('storage/' . $file->path_file),
                    'tipe' => $file->tipe,
                ];
            })->values(),
            'created_at' => $note->created_at->toIso8601String(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Detail note dalam antrian proses.',
            'data' => $data,
        ]);
    }

    public function handleQueueSubmission(Request $request, string $id)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $note = Note::with('noteStatus')->find($id);

        if (!$note || !$note->noteStatus || $note->noteStatus->status !== 'diproses') {
            return response()->json([
                'success' => false,
                'message' => 'Note tidak ditemukan atau tidak dalam antrian proses.',
            ], 404);
        }

        // Ubah status sesuai action
        if ($validated['action'] === 'approve') {
            $note->noteStatus->status = 'diterima';
        } else {
            $note->noteStatus->status = 'ditolak';
        }
        $note->noteStatus->save();

        return response()->json([
            'success' => true,
            'message' => 'Status note berhasil diperbarui.',
            'data' => [
                'note_id' => $note->note_id,
                'status' => $note->noteStatus->status,
            ],
        ]);
    }

    public function getAllUsers(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string',
            'size' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        $size = $validated['size'] ?? 20;
        $page = $validated['page'] ?? 1;
        $search = $validated['search'] ?? "";

        $reportedUserIds = Report::distinct()->pluck('reported_user_id')->toArray();

        $user = User::query()
            ->where('role', 'student')
            ->where(function ($q) use ($search) {
                if ($search) {
                    $q->where('nama', 'like', "%$search%")
                        ->orWhere('username', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                }
            })
            ->with('notes.noteStatus')
            ->orderByRaw('FIELD(user_id, ' . implode(',', $reportedUserIds) . ') DESC');
        // ->orderBy('username', 'asc');

        $paginated = $user->paginate($size, ['*'], 'page', $page);

        $result = $paginated->getCollection()->map(function ($user) {
            $jumlahNotes = $user->notes->filter(function ($note) {
                return $note->noteStatus && $note->noteStatus->status === 'diterima';
            })->count();

            return [
                'user_id' => $user->user_id,
                'nama' => $user->nama,
                'username' => $user->username,
                'foto_profil' => $user->foto_profil ? url('storage/' . $user->foto_profil) : null,
                'jumlah_notes' => $jumlahNotes,
                'isBanned' => $user->status_akun === 'nonaktif',
            ];
        });

        $paginationMeta = [
            'current_page' => $paginated->currentPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'last_page' => $paginated->lastPage(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem(),
            'has_more_pages' => $paginated->hasMorePages(),
            'path' => $paginated->path(),
            'links' => [
                'first' => $paginated->url(1),
                'last' => $paginated->url($paginated->lastPage()),
                'prev' => $paginated->previousPageUrl(),
                'next' => $paginated->nextPageUrl(),
            ]
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar users',
            'data' => $result,
            'pagination' => $paginationMeta,
        ]);
    }

    public function banUser(Request $request, string $id)
    {
        $user = User::query()->where('user_id', $id)->first();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "User tidak ditemukan",
            ], 404);
        }

        if ($user->status_akun != 'aktif') {
            return response()->json([
                "success" => false,
                "message" => "User sudah di ban",
            ], 400);
        }

        $user->status_akun = 'nonaktif';
        $user->save();

        return response()->json([
            "success" => true,
            "message" => "User berhasil di-ban",
            "data" => [
                "user_id" => $user->user_id,
                "status_akun" => $user->status_akun,
            ]
        ]);
    }

    public function unbanUser(Request $request, string $id)
    {
        $user = User::query()->where('user_id', '=', $id)->first();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "User tidak ditemukan",
            ], 404);
        }

        if ($user->status_akun == 'aktif') {
            return response()->json([
                "success" => false,
                "message" => "User sedang tidak dalam status banned",
            ], 400);
        }

        $user->status_akun = 'aktif';
        $user->save();

        return response()->json([
            "success" => true,
            "message" => "User berhasil di-unban",
            "data" => [
                "user_id" => $user->user_id,
                "status_akun" => $user->status_akun,
            ]
        ]);
    }
}
