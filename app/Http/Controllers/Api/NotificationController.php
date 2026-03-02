<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $limit = $request->get('limit', 20);

        $notifications = Notification::forUser($user->id)
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($n) { return $this->format($n); });

        $unreadCount = Notification::forUser($user->id)->unread()->count();

        return response()->json([
            'success'      => true,
            'data'         => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function unreadCount(Request $request)
    {
        $count = Notification::forUser($request->user()->id)->unread()->count();

        return response()->json([
            'success'      => true,
            'unread_count' => $count,
        ]);
    }

    public function markRead(Request $request, $id)
    {
        $notif = Notification::where('user_id', $request->user()->id)->findOrFail($id);
        $notif->markAsRead();

        return response()->json(['success' => true, 'message' => 'Notifikasi ditandai sudah dibaca.']);
    }

    public function markAllRead(Request $request)
    {
        Notification::forUser($request->user()->id)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Semua notifikasi sudah ditandai dibaca.']);
    }

    public function destroy(Request $request, $id)
    {
        Notification::where('user_id', $request->user()->id)->findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Notifikasi dihapus.']);
    }

    private function format(Notification $n)
    {
        return [
            'id'         => $n->id,
            'type'       => $n->type,
            'title'      => $n->title,
            'message'    => $n->message,
            'data'       => $n->data,
            'is_read'    => $n->is_read,
            'read_at'    => $n->read_at ? $n->read_at->toISOString() : null,
            'created_at' => $n->created_at->toISOString(),
            'time_ago'   => $n->created_at->diffForHumans(),
        ];
    }
}
