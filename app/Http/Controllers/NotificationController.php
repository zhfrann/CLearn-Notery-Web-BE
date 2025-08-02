<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function getAllAnnouncement(Request $request)
    {
        try {
            $announcements = Notification::query()->where('type', '=', 'announcement')->get();
            $data = $announcements->map(function ($announcement) {
                return [
                    'notification_id' => $announcement->notification_id,
                    'type' => $announcement->type,
                    'title' => $announcement->title,
                    'body' => $announcement->body,
                    'files' => $announcement->files,
                    'created_at' => $announcement->created_at->toIso8601String()
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Daftar pengumuman',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Gagal mendapatkan data pengumuman",
            ]);
        }
    }

    public function createAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240'
        ]);

        DB::beginTransaction();
        try {
            // Pengumuman: user_id = null, type = 'announcement'
            $notification = Notification::create([
                'user_id' => null,
                'type' => 'announcement',
                'title' => $request->title,
                'body' => $request->body,
                'is_read' => false
            ]);

            $notificationFiles = null;

            // Upload file jika ada
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('notification_files', 'public');
                    $notificationFiles = NotificationFile::create([
                        'notification_id' => $notification->notification_id,
                        'nama_file' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'tipe' => $file->getClientMimeType(),
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengumuman berhasil dibuat',
                'data' => [
                    'notification_id' => $notification->notification_id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'files' => $notification->files,
                    'created_at' => $notification->created_at->toIso8601String()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pengumuman',
                // 'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createWarning(Request $request, string $id)
    {
        $request->validate([
            'body' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $notification = Notification::create([
                'user_id' => (int)$id,
                'type' => 'warning',
                'body' => $request->body,
                'is_read' => false,
                'data' => [
                    'admin' => [
                        'user_id' => $request->user()->user_id ?? null, // jika pakai auth
                        'username' => $request->user()->username ?? 'Admin',
                    ],
                ]
            ]);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('notification_files', 'public');
                    NotificationFile::create([
                        'notification_id' => $notification->notification_id,
                        'nama_file' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'tipe' => $file->getClientMimeType(),
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Warning berhasil dikirim',
                'data' => [
                    'notification_id' => $notification->notification_id,
                    'warned_user_id' => $notification->user_id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'files' => $notification->files,
                    'created_at' => $notification->created_at->toIso8601String(),
                    'admin' => $notification->data['admin'] ?? null,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim warning',
                // 'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserNotification(Request $request)
    {
        $id = $request->user()->user_id;

        try {
            Notification::where('user_id', (int)$id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            // Ambil notifikasi untuk user (personal) dan announcement (global)
            $notifications = Notification::query()
                ->where(function ($q) use ($id) {
                    $q->where('user_id', (int)$id)
                        ->orWhereNull('user_id'); // announcement
                })
                ->orderByDesc('created_at')
                ->get();

            $data = $notifications->map(function ($notification) {
                $buyer = $notification->data['buyer'] ?? null;
                if ($buyer && isset($buyer['foto_profil'])) {
                    $buyer['foto_profil'] = url(asset('storage/' . $buyer['foto_profil']));
                }
                return [
                    'notification_id' => $notification->notification_id,
                    'user_id' => $notification->user_id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'files' => $notification->files,
                    'created_at' => $notification->created_at->toIso8601String(),
                    'buyer' => $buyer,
                    'chat_message' => $notification->data['chat_message'] ?? null,
                    'note' => $notification->data['note'] ?? null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Daftar notifikasi user',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan data notifikasi',
            ], 500);
        }
    }

    public function notifyFirstChat($sellerId, $buyer, $message)
    {
        $notification = Notification::create([
            'user_id' => $sellerId,
            'type' => 'info',
            'title' => "{$buyer->nama} tertarik pada produk anda",
            'body' => $message->pesan,
            'is_read' => false,
            'data' => [
                'buyer' => [
                    'user_id' => $buyer->user_id,
                    // 'nama' => $buyer->nama,
                    'username' => $buyer->username,
                    'foto_profil' => $buyer->foto_profil,
                ],
                'chat_message' => [
                    'message_id' => $message->message_id,
                    'pesan' => $message->pesan,
                    'created_at' => $message->created_at->toIso8601String(),
                ]
            ]
        ]);
        return $notification;
    }

    public function notifyFavoriteNote($sellerId, $buyer, $note)
    {
        $notification = Notification::create([
            'user_id' => $sellerId,
            'type' => 'info',
            'title' => "{$buyer->username} menambahkan produk anda dalam favorit",
            'body' => "Produk: {$note->judul}",
            'is_read' => false,
            'data' => [
                'buyer' => [
                    'user_id' => $buyer->user_id,
                    'username' => $buyer->username,
                    'foto_profil' => $buyer->foto_profil,
                ],
                'note' => [
                    'note_id' => $note->note_id,
                    'judul' => $note->judul,
                ]
            ]
        ]);
        return $notification;
    }

    public function notifyPayment($sellerId, $buyer, $note)
    {
        $notification = Notification::create([
            'user_id' => $sellerId,
            'type' => 'info',
            'title' => "{$buyer->username} telah melakukan pembayaran",
            'body' => "Pembelian produk: {$note->judul}",
            'is_read' => false,
            'data' => [
                'buyer' => [
                    'user_id' => $buyer->user_id,
                    'username' => $buyer->username,
                    'foto_profil' => $buyer->foto_profil,
                ],
                'note' => [
                    'note_id' => $note->note_id,
                    'judul' => $note->judul,
                ]
            ]
        ]);
        return $notification;
    }
}
