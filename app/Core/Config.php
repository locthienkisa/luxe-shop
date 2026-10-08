<?php

declare(strict_types=1);

namespace App\Core;

final class Config
{
    private static array $items = [];

    public static function load(string $root): void
    {
        $envFile = $root . '/.env';
        if (is_file($envFile)) {
            foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }
                if (!str_contains($line, '=')) {
                    continue;
                }
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                $value = trim($value, "\"'");
                $_ENV[$key] = $value;
                putenv($key . '=' . $value);
            }
        }

        $app = require $root . '/config/app.php';
        $db = require $root . '/config/database.php';
        self::$items = ['app' => $app, 'database' => $db];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key);
        $value = self::$items;
        foreach ($parts as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }
        return $value;
    }

    public static function env(string $key, mixed $default = null): mixed
    {
        $val = $_ENV[$key] ?? getenv($key);
        if ($val === false || $val === null || $val === '') {
            return $default;
        }
        return match (strtolower((string) $val)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'null', '(null)' => null,
            default => $val,
        };
    }
}
