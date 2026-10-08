<?php

declare(strict_types=1);

namespace App\Models;

final class ApiToken extends Model
{
    protected static string $table = 'api_tokens';

    public static function issue(int $userId, int $hours = 72): string
    {
        $plain = bin2hex(random_bytes(32));
        $hash = hash('sha256', $plain);
        self::db()->prepare(
            'INSERT INTO api_tokens (user_id, token_hash, expires_at) VALUES (:u,:h, DATE_ADD(NOW(), INTERVAL :hrs HOUR))'
        )->execute(['u' => $userId, 'h' => $hash, 'hrs' => $hours]);
        return $plain;
    }

    public static function userByPlain(string $plain): ?array
    {
        $hash = hash('sha256', $plain);
        $st = self::db()->prepare(
            'SELECT u.*, r.slug AS role_slug FROM api_tokens t
             JOIN users u ON u.id=t.user_id JOIN roles r ON r.id=u.role_id
             WHERE t.token_hash=:h AND t.expires_at > NOW() AND u.is_active=1 LIMIT 1'
        );
        $st->execute(['h' => $hash]);
        $row = $st->fetch();
        return $row ?: null;
    }
}
