<?php

declare(strict_types=1);

namespace App\Models;

final class Setting extends Model
{
    protected static string $table = 'settings';
    private static array $cache = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }
        try {
            $st = self::db()->prepare('SELECT `value` FROM settings WHERE `key` = :k');
            $st->execute(['k' => $key]);
            $val = $st->fetchColumn();
            self::$cache[$key] = $val === false ? $default : $val;
        } catch (\Throwable) {
            return $default;
        }
        return self::$cache[$key];
    }

    public static function set(string $key, ?string $value): void
    {
        $st = self::db()->prepare(
            'INSERT INTO settings (`key`, `value`) VALUES (:k, :v) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)'
        );
        $st->execute(['k' => $key, 'v' => $value]);
        self::$cache[$key] = $value;
    }

    public static function allKV(): array
    {
        $rows = self::db()->query('SELECT `key`, `value` FROM settings')->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $out[$r['key']] = $r['value'];
        }
        return $out;
    }
}
