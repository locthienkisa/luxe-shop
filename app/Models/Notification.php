<?php

declare(strict_types=1);

namespace App\Models;

final class Notification extends Model
{
    protected static string $table = 'notifications';

    public static function create(int $userId, string $title, string $body, ?string $link = null): void
    {
        self::db()->prepare(
            'INSERT INTO notifications (user_id,title,body,link) VALUES (:u,:t,:b,:l)'
        )->execute(['u' => $userId, 't' => $title, 'b' => $body, 'l' => $link]);
    }

    public static function forUser(int $userId, int $limit = 30): array
    {
        $st = self::db()->prepare('SELECT * FROM notifications WHERE user_id=:u ORDER BY id DESC LIMIT ' . (int) $limit);
        $st->execute(['u' => $userId]);
        return $st->fetchAll();
    }

    public static function unread(int $userId): int
    {
        $st = self::db()->prepare('SELECT COUNT(*) FROM notifications WHERE user_id=:u AND is_read=0');
        $st->execute(['u' => $userId]);
        return (int) $st->fetchColumn();
    }

    public static function markRead(int $id, int $userId): void
    {
        self::db()->prepare('UPDATE notifications SET is_read=1 WHERE id=:id AND user_id=:u')->execute(['id' => $id, 'u' => $userId]);
    }

    public static function markAll(int $userId): void
    {
        self::db()->prepare('UPDATE notifications SET is_read=1 WHERE user_id=:u')->execute(['u' => $userId]);
    }
}
