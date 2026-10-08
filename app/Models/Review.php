<?php

declare(strict_types=1);

namespace App\Models;

final class Review extends Model
{
    protected static string $table = 'reviews';

    public static function approvedForProduct(int $productId): array
    {
        $st = self::db()->prepare(
            'SELECT r.*, u.full_name, u.avatar FROM reviews r JOIN users u ON u.id=r.user_id
             WHERE r.product_id=:p AND r.status=\'approved\' ORDER BY r.id DESC'
        );
        $st->execute(['p' => $productId]);
        $rows = $st->fetchAll();
        foreach ($rows as &$r) {
            $r['images'] = self::images((int) $r['id']);
        }
        return $rows;
    }

    public static function images(int $reviewId): array
    {
        $st = self::db()->prepare('SELECT * FROM review_images WHERE review_id=:id');
        $st->execute(['id' => $reviewId]);
        return $st->fetchAll();
    }

    public static function owned(int $id, int $userId): ?array
    {
        $st = self::db()->prepare('SELECT * FROM reviews WHERE id=:id AND user_id=:u');
        $st->execute(['id' => $id, 'u' => $userId]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function latestApproved(int $limit = 6): array
    {
        return self::db()->query(
            'SELECT r.*, u.full_name, p.name AS product_name, p.slug AS product_slug
             FROM reviews r JOIN users u ON u.id=r.user_id JOIN products p ON p.id=r.product_id
             WHERE r.status=\'approved\' ORDER BY r.id DESC LIMIT ' . (int) $limit
        )->fetchAll();
    }

    public static function adminList(string $status, int $offset, int $limit): array
    {
        $where = '1=1';
        $params = [];
        if ($status !== '') {
            $where = 'r.status = :s';
            $params['s'] = $status;
        }
        $c = self::db()->prepare("SELECT COUNT(*) FROM reviews r WHERE $where");
        $c->execute($params);
        $total = (int) $c->fetchColumn();
        $st = self::db()->prepare(
            "SELECT r.*, u.email, p.name AS product_name FROM reviews r
             JOIN users u ON u.id=r.user_id JOIN products p ON p.id=r.product_id
             WHERE $where ORDER BY r.id DESC LIMIT $limit OFFSET $offset"
        );
        $st->execute($params);
        return ['total' => $total, 'rows' => $st->fetchAll()];
    }
}
