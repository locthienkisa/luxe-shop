<?php

declare(strict_types=1);

namespace App\Core;

final class Logger
{
    public static function write(string $level, string $message, array $context = []): void
    {
        $dir = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $line = sprintf(
            "[%s] %s: %s %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            $context ? json_encode(self::redact($context), JSON_UNESCAPED_UNICODE) : ''
        );
        file_put_contents($dir . '/app.log', $line, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $message, array $context = []): void
    {
        self::write('info', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('error', $message, $context);
    }

    public static function security(string $message, array $context = []): void
    {
        self::write('security', $message, $context);
    }

    private static function redact(array $context): array
    {
        $sensitive = ['password', 'password_hash', 'token', 'secret', 'api_key', 'csrf'];
        foreach ($context as $k => $v) {
            foreach ($sensitive as $s) {
                if (stripos((string) $k, $s) !== false) {
                    $context[$k] = '[redacted]';
                }
            }
        }
        return $context;
    }
}
