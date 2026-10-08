<?php

declare(strict_types=1);

namespace App\Models;

final class LoginAttempt extends Model
{
    protected static string $table = 'login_attempts';

    public static function record(string $email, string $ip, bool $success): void
    {
        self::db()->prepare(
            'INSERT INTO login_attempts (email, ip, success) VALUES (:e,:ip,:s)'
        )->execute(['e' => $email, 'ip' => $ip, 's' => $success ? 1 : 0]);
    }

    public static function tooMany(string $email, string $ip, int $max, int $minutes): bool
    {
        $st = self::db()->prepare(
            'SELECT COUNT(*) FROM login_attempts
             WHERE success=0 AND created_at > DATE_SUB(NOW(), INTERVAL :m MINUTE)
             AND (email=:e OR ip=:ip)'
        );
        $st->execute(['m' => $minutes, 'e' => $email, 'ip' => $ip]);
        return (int) $st->fetchColumn() >= $max;
    }
}
