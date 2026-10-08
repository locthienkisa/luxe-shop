<?php

declare(strict_types=1);

namespace App\Models;

final class PasswordReset extends Model
{
    protected static string $table = 'password_resets';

    public static function createToken(int $userId): string
    {
        $plain = bin2hex(random_bytes(32));
        $hash = hash('sha256', $plain);
        self::db()->prepare(
            'INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (:u,:h, DATE_ADD(NOW(), INTERVAL 1 HOUR))'
        )->execute(['u' => $userId, 'h' => $hash]);
        return $plain;
    }

    public static function consume(string $plain): ?array
    {
        $hash = hash('sha256', $plain);
        $st = self::db()->prepare(
            'SELECT * FROM password_resets WHERE token_hash=:h AND used_at IS NULL AND expires_at > NOW() ORDER BY id DESC LIMIT 1'
        );
        $st->execute(['h' => $hash]);
        $row = $st->fetch();
        if (!$row) {
            return null;
        }
        self::db()->prepare('UPDATE password_resets SET used_at=NOW() WHERE id=:id')->execute(['id' => $row['id']]);
        return $row;
    }
}
