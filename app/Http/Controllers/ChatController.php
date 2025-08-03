<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\MessageFile;
use App\Models\Note;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Endpoint untuk mendapatkan/membuat chat room antara buyer dan seller
    public function getOrCreateChatRoom(Request $request, $noteId)
    {
        $buyer = $request->user();
        $note = Note::query()->find($noteId);

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => "Note tidak ditemukan"
            ], 404);
        }

        $seller = $note->seller;

        // Cek jika buyer == seller, tidak boleh chat dengan diri sendiri
        if ($buyer->user_id == $seller->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal buat/dapatkan informasi chat room. Anda tidak bisa chat dengan diri sendiri'
            ], 400);
        }

        // Cari chat room yang sudah ada
        $chatRoom = ChatRoom::where(function ($q) use ($buyer, $seller) {
            $q->where('user_one_id', $buyer->user_id)
                ->where('user_two_id', $seller->user_id);
        })->orWhere(function ($q) use ($buyer, $seller) {
            $q->where('user_one_id', $seller->user_id)
                ->where('user_two_id', $buyer->user_id);
        })->first();

        // Jika belum ada, buat baru
        if (!$chatRoom) {
            $chatRoom = ChatRoom::create([
                'user_one_id' => $buyer->user_id,
                'user_two_id' => $seller->user_id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil membuat chat room',
            'data' => [
                'chat_room_id' => $chatRoom->chat_room_id,
                'buyer_id' => $buyer->user_id,
                'seller_id' => $seller->user_id,
            ]
        ]);
    }

    public function sendMessage(Request $request, $chatRoomId)
    {
        $request->validate([
            'pesan' => 'required|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:5120', // max 5MB
        ]);
        $user = $request->user();

        $chatRoom = ChatRoom::query()->find($chatRoomId);

        if (!$chatRoom) {
            return response()->json([
                'success' => false,
                'message' => "Room $chatRoomId tidak ditemukan"
            ], 404);
        }

        // Pastikan user adalah bagian dari chat room
        if ($chatRoom->user_one_id !== $user->user_id && $chatRoom->user_two_id !== $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak bisa mengakses chat room yang bukan punya anda'
            ], 403);
        }

        $message = Message::create([
            'chat_room_id' => $chatRoom->chat_room_id,
            'sender_id' => $user->user_id,
            'pesan' => $request->pesan,
        ]);

        $fileData = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('chat/files', $filename, 'public');

            $messageFile = MessageFile::create([
                'message_id' => $message->message_id,
                'nama_file' => $file->getClientOriginalName(),
                'path_file' => $path,
                'tipe' => $file->getClientOriginalExtension(),
            ]);

            $fileData = [
                'message_file_id' => $messageFile->message_file_id,
                'nama_file' => $messageFile->nama_file,
                'path_file' => url('storage/' . $messageFile->path_file),
                'tipe' => $messageFile->tipe,
            ];
        }

        // Cek apakah ini pesan pertama di chat room
        if ($chatRoom->messages()->count() == 1) {
            // Tentukan seller (selalu user_one_id atau user_two_id yang bukan pengirim)
            $sellerId = ($chatRoom->user_one_id == $user->user_id) ? $chatRoom->user_two_id : $chatRoom->user_one_id;
            // Panggil NotificationController untuk membuat notifikasi
            app('App\Http\Controllers\NotificationController')->notifyFirstChat($sellerId, $user, $message);
        }

        // Broadcast event ke channel chat
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'data' => [
                'message_id' => $message->message_id,
                'chat_room_id' => $message->chat_room_id,
                'sender_id' => $message->sender_id,
                'pesan' => $message->pesan,
                'file' => $fileData,
                'created_at' => $message->created_at,
            ]
        ]);
    }

    public function getMessages(Request $request, $chatRoomId)
    {
        $user = $request->user();
        $chatRoom = ChatRoom::query()->find($chatRoomId);

        if (!$chatRoom) {
            return response()->json([
                'success' => false,
                'message' => "Room $chatRoomId tidak ditemukan"
            ], 404);
        }

        // Pastikan user adalah bagian dari chat room
        if ($chatRoom->user_one_id !== $user->user_id && $chatRoom->user_two_id !== $user->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $chatRoom->messages()->with('files')->orderBy('created_at')->get();

        $data = $messages->map(function ($msg) {
            return [
                'message_id' => $msg->message_id,
                'chat_room_id' => $msg->chat_room_id,
                'sender_id' => $msg->sender_id,
                'pesan' => $msg->pesan,
                'file' => $msg->files->map(function ($file) {
                    return [
                        'message_file_id' => $file->message_file_id,
                        'nama_file' => $file->nama_file,
                        'path_file' => url('storage/' . $file->path_file),
                        'tipe' => $file->tipe,
                    ];
                })->first(), // Ambil satu file saja (karena satu pesan satu file gambar)
                'created_at' => $msg->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
