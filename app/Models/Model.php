<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

abstract class Model
{
    protected static string $table = '';
    protected static string $pk = 'id';

    public static function db(): PDO
    {
        return Database::connection();
    }

    public static function find(int $id): ?array
    {
        $sql = 'SELECT * FROM `' . static::$table . '` WHERE `' . static::$pk . '` = :id LIMIT 1';
        $st = self::db()->prepare($sql);
        $st->execute(['id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function all(string $order = 'id DESC', int $limit = 500): array
    {
        $sql = 'SELECT * FROM `' . static::$table . '` ORDER BY ' . $order . ' LIMIT ' . (int) $limit;
        return self::db()->query($sql)->fetchAll();
    }
}
