<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Kirim notifikasi ke satu user
     */
    public static function send(int $userId, string $type, string $title, string $message, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'data'    => $data,
        ]);
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
