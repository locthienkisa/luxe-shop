<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Models\Notification;

final class NotificationService
{
    public static function unreadCount(): int
    {
        if (!Auth::id()) {
            return 0;
        }
        try {
            return Notification::unread(Auth::id());
        } catch (\Throwable) {
            return 0;
        }
    }

    public static function send(int $userId, string $title, string $body, ?string $link = null): void
    {
        Notification::create($userId, $title, $body, $link);
    }
}
