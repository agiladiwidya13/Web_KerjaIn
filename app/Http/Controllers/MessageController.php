<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * GET /api/messages/contacts
     * Ambil daftar kontak yang pernah berkirim pesan dengan user (Inbox view)
     */
    public function contacts()
    {
        $userId = session('user_id');

        // Cari semua user_id yang pernah interaksi (baik sebagai sender maupun receiver)
        $contactIds = DB::table('messages')
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->select('sender_id', 'receiver_id')
            ->get()
            ->flatMap(function ($item) use ($userId) {
                return [$item->sender_id, $item->receiver_id];
            })
            ->filter(function ($id) use ($userId) {
                return $id !== $userId;
            })
            ->unique();

        $contacts = User::whereIn('id', $contactIds)->get()->map(function ($u) use ($userId) {
            // Dapatkan pesan terakhir
            $lastMessage = Message::where(function($q) use ($userId, $u) {
                    $q->where('sender_id', $userId)->where('receiver_id', $u->id);
                })
                ->orWhere(function($q) use ($userId, $u) {
                    $q->where('sender_id', $u->id)->where('receiver_id', $userId);
                })
                ->orderByDesc('created_at')
                ->first();

            $unreadCount = Message::where('sender_id', $u->id)
                ->where('receiver_id', $userId)
                ->whereNull('read_at')
                ->count();

            return [
                'id' => $u->id,
                'nama' => $u->nama_lengkap,
                'role' => $u->role,
                'last_message' => $lastMessage ? $lastMessage->isi : '',
                'last_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : '',
                'unread' => $unreadCount
            ];
        })->sortByDesc('last_time')->values();

        return response()->json([
            'status' => 'success',
            'data' => $contacts
        ]);
    }

    /**
     * GET /api/messages/{userId}
     * Ambil riwayat chat dengan user tertentu
     */
    public function history($contactId)
    {
        $userId = session('user_id');

        // Tandai sudah dibaca
        Message::where('sender_id', $contactId)
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where(function($q) use ($userId, $contactId) {
                $q->where('sender_id', $userId)->where('receiver_id', $contactId);
            })
            ->orWhere(function($q) use ($userId, $contactId) {
                $q->where('sender_id', $contactId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($m) use ($userId) {
                return [
                    'id' => $m->id,
                    'is_mine' => $m->sender_id === $userId,
                    'isi' => $m->isi,
                    'time' => $m->created_at->format('H:i'),
                    'is_read' => $m->read_at !== null
                ];
            });

        $contact = User::find($contactId);

        return response()->json([
            'status' => 'success',
            'contact' => [
                'id' => $contact->id,
                'nama' => $contact->nama_lengkap,
                'role' => $contact->role
            ],
            'data' => $messages
        ]);
    }

    /**
     * POST /api/messages/{userId}
     * Kirim pesan ke user tertentu
     */
    public function send(Request $request, $receiverId)
    {
        $request->validate(['isi' => 'required|string']);
        $userId = session('user_id');

        $message = Message::create([
            'id' => (string) Str::uuid(),
            'sender_id' => $userId,
            'receiver_id' => $receiverId,
            'isi' => $request->isi,
            'created_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $message->id,
                'is_mine' => true,
                'isi' => $message->isi,
                'time' => now()->format('H:i'),
                'is_read' => false
            ]
        ]);
    }
}
