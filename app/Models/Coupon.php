<?php

declare(strict_types=1);

namespace App\Models;

final class Coupon extends Model
{
    protected static string $table = 'coupons';

    public static function findByCode(string $code): ?array
    {
        $st = self::db()->prepare('SELECT * FROM coupons WHERE code = :c LIMIT 1');
        $st->execute(['c' => strtoupper(trim($code))]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function userUsage(int $couponId, int $userId): int
    {
        $st = self::db()->prepare('SELECT COUNT(*) FROM coupon_usages WHERE coupon_id = :c AND user_id = :u');
        $st->execute(['c' => $couponId, 'u' => $userId]);
        return (int) $st->fetchColumn();
    }

    public static function create(array $data): int
    {
        $st = self::db()->prepare(
            'INSERT INTO coupons (code,discount_type,discount_value,min_order,max_discount,starts_at,ends_at,usage_limit,per_user_limit,is_active)
             VALUES (:code,:discount_type,:discount_value,:min_order,:max_discount,:starts_at,:ends_at,:usage_limit,:per_user_limit,:is_active)'
        );
        $st->execute($data);
        return (int) self::db()->lastInsertId();
    }

    public static function updateRow(int $id, array $data): void
    {
        $data['id'] = $id;
        self::db()->prepare(
            'UPDATE coupons SET code=:code,discount_type=:discount_type,discount_value=:discount_value,min_order=:min_order,
             max_discount=:max_discount,starts_at=:starts_at,ends_at=:ends_at,usage_limit=:usage_limit,per_user_limit=:per_user_limit,is_active=:is_active
             WHERE id=:id'
        )->execute($data);
    }

    public static function delete(int $id): void
    {
        self::db()->prepare('DELETE FROM coupons WHERE id=:id')->execute(['id' => $id]);
    }
}
