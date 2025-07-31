<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\Note;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Endpoint untuk mendapatkan/membuat chat room antara buyer dan seller
    public function getOrCreateChatRoom(Request $request, $noteId)
    {
        $buyer = $request->user();
        $note = Note::findOrFail($noteId);
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
        ]);
        $user = $request->user();

        $chatRoom = \App\Models\ChatRoom::findOrFail($chatRoomId);

        // Pastikan user adalah bagian dari chat room
        if ($chatRoom->user_one_id !== $user->user_id && $chatRoom->user_two_id !== $user->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'chat_room_id' => $chatRoom->chat_room_id,
            'sender_id' => $user->user_id,
            'pesan' => $request->pesan,
        ]);

        // Broadcast event ke channel chat
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'data' => [
                'message_id' => $message->message_id,
                'chat_room_id' => $message->chat_room_id,
                'sender_id' => $message->sender_id,
                'pesan' => $message->pesan,
                'created_at' => $message->created_at,
            ]
        ]);
    }

    public function getMessages(Request $request, $chatRoomId)
    {
        $user = $request->user();
        $chatRoom = ChatRoom::query()->findOrFail($chatRoomId);

        if (!$chatRoom) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan pesan'
            ], 404);
        }

        // Pastikan user adalah bagian dari chat room
        if ($chatRoom->user_one_id !== $user->user_id && $chatRoom->user_two_id !== $user->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $chatRoom->messages()->orderBy('created_at')->get();

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }
}
