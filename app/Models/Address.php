<?php

declare(strict_types=1);

namespace App\Models;

final class Address extends Model
{
    protected static string $table = 'addresses';

    public static function forUser(int $userId): array
    {
        $st = self::db()->prepare('SELECT * FROM addresses WHERE user_id = :u ORDER BY is_default DESC, id DESC');
        $st->execute(['u' => $userId]);
        return $st->fetchAll();
    }

    public static function owned(int $id, int $userId): ?array
    {
        $st = self::db()->prepare('SELECT * FROM addresses WHERE id = :id AND user_id = :u');
        $st->execute(['id' => $id, 'u' => $userId]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        if (!empty($data['is_default'])) {
            self::db()->prepare('UPDATE addresses SET is_default = 0 WHERE user_id = :u')->execute(['u' => $data['user_id']]);
        }
        $st = self::db()->prepare(
            'INSERT INTO addresses (user_id,label,full_name,phone,line1,line2,ward,district,city,postal_code,is_default)
             VALUES (:user_id,:label,:full_name,:phone,:line1,:line2,:ward,:district,:city,:postal_code,:is_default)'
        );
        $st->execute($data);
        return (int) self::db()->lastInsertId();
    }

    public static function updateRow(int $id, int $userId, array $data): void
    {
        if (!empty($data['is_default'])) {
            self::db()->prepare('UPDATE addresses SET is_default = 0 WHERE user_id = :u')->execute(['u' => $userId]);
        }
        $data['id'] = $id;
        $data['user_id'] = $userId;
        self::db()->prepare(
            'UPDATE addresses SET label=:label,full_name=:full_name,phone=:phone,line1=:line1,line2=:line2,ward=:ward,
             district=:district,city=:city,postal_code=:postal_code,is_default=:is_default WHERE id=:id AND user_id=:user_id'
        )->execute($data);
    }

    public static function deleteOwned(int $id, int $userId): void
    {
        self::db()->prepare('DELETE FROM addresses WHERE id=:id AND user_id=:u')->execute(['id' => $id, 'u' => $userId]);
    }

    public static function format(array $a): string
    {
        return implode(', ', array_filter([$a['line1'], $a['line2'], $a['ward'], $a['district'], $a['city']]));
    }
}
