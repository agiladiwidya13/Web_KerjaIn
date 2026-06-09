<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * GET /api/notifications
     * Ambil notifikasi milik user yang sedang login
     */
    public function index()
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $notifications = Notification::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->data['title'] ?? 'Notifikasi Baru',
                    'message' => $n->data['message'] ?? '',
                    'link' => $n->data['link'] ?? '#',
                    'is_read' => $n->read_at !== null,
                    'created_at' => $n->created_at?->diffForHumans()
                ];
            });

        $unreadCount = Notification::where('user_id', $userId)->whereNull('read_at')->count();

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * POST /api/notifications/{id}/read
     * Tandai notifikasi sebagai terbaca
     */
    public function markAsRead($id)
    {
        $userId = session('user_id');
        $notification = Notification::where('user_id', $userId)->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['status' => 'success', 'message' => 'Notifikasi ditandai sudah dibaca']);
    }
}
