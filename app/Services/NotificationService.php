<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Events\NotificationSent;

class NotificationService
{
    /**
     * Kirim notifikasi ke satu user
     */
    public static function send(int $userId, string $type, string $title, string $message, array $data = []): Notification
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'data'    => $data,
        ]);

        // 🔔 Broadcast real-time via Pusher
        broadcast(new NotificationSent($notification, $userId));

        return $notification;
    }

    /**
     * Kirim notifikasi ke semua admin (role = 'admin')
     */
    public static function sendToAllAdmins(string $type, string $title, string $message, array $data = []): void
    {
        $admins = User::where('role_id', 1)->pluck('id');

        foreach ($admins as $adminId) {
            self::send($adminId, $type, $title, $message, $data);
        }
    }

    /**
     * Kirim notifikasi ke semua user dengan role tertentu
     */
    public static function sendToRole(string $role, string $type, string $title, string $message, array $data = []): void
    {
        $users = User::where('role_id', $role)->pluck('id');

        foreach ($users as $userId) {
            self::send($userId, $type, $title, $message, $data);
        }
    }
}
