<?php

declare(strict_types=1);

namespace App\Models;

final class Banner extends Model
{
    protected static string $table = 'banners';

    public static function active(string $position): array
    {
        $st = self::db()->prepare(
            "SELECT * FROM banners WHERE is_active=1 AND position=:p
             AND (starts_at IS NULL OR starts_at <= NOW())
             AND (ends_at IS NULL OR ends_at >= NOW())
             ORDER BY sort_order, id"
        );
        $st->execute(['p' => $position]);
        return $st->fetchAll();
    }

    public static function create(array $data): int
    {
        $st = self::db()->prepare(
            'INSERT INTO banners (title,subtitle,image,link,position,sort_order,is_active,starts_at,ends_at)
             VALUES (:title,:subtitle,:image,:link,:position,:sort_order,:is_active,:starts_at,:ends_at)'
        );
        $st->execute($data);
        return (int) self::db()->lastInsertId();
    }

    public static function updateRow(int $id, array $data): void
    {
        $data['id'] = $id;
        self::db()->prepare(
            'UPDATE banners SET title=:title,subtitle=:subtitle,image=:image,link=:link,position=:position,
             sort_order=:sort_order,is_active=:is_active,starts_at=:starts_at,ends_at=:ends_at WHERE id=:id'
        )->execute($data);
    }

    public static function delete(int $id): void
    {
        self::db()->prepare('DELETE FROM banners WHERE id=:id')->execute(['id' => $id]);
    }
}
