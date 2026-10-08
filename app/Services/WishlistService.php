<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;

final class WishlistService
{
    public static function count(): int
    {
        if (!Auth::id()) {
            return 0;
        }
        try {
            $st = Database::connection()->prepare(
                'SELECT COUNT(*) FROM wishlist_items wi JOIN wishlists w ON w.id=wi.wishlist_id WHERE w.user_id=:u'
            );
            $st->execute(['u' => Auth::id()]);
            return (int) $st->fetchColumn();
        } catch (\Throwable) {
            return 0;
        }
    }

    public static function id(): int
    {
        $uid = Auth::id();
        if (!$uid) {
            throw new \RuntimeException('Cần đăng nhập.');
        }
        $pdo = Database::connection();
        $st = $pdo->prepare('SELECT id FROM wishlists WHERE user_id=:u');
        $st->execute(['u' => $uid]);
        $id = $st->fetchColumn();
        if ($id) {
            return (int) $id;
        }
        $pdo->prepare('INSERT INTO wishlists (user_id) VALUES (:u)')->execute(['u' => $uid]);
        return (int) $pdo->lastInsertId();
    }

    public static function toggle(int $productId): bool
    {
        $wid = self::id();
        $pdo = Database::connection();
        $st = $pdo->prepare('SELECT id FROM wishlist_items WHERE wishlist_id=:w AND product_id=:p');
        $st->execute(['w' => $wid, 'p' => $productId]);
        $id = $st->fetchColumn();
        if ($id) {
            $pdo->prepare('DELETE FROM wishlist_items WHERE id=:id')->execute(['id' => $id]);
            return false;
        }
        $pdo->prepare('INSERT INTO wishlist_items (wishlist_id, product_id) VALUES (:w,:p)')->execute(['w' => $wid, 'p' => $productId]);
        return true;
    }

    public static function items(): array
    {
        $wid = self::id();
        $st = Database::connection()->prepare(
            'SELECT p.*, wi.id AS wishlist_item_id,
                    (SELECT path FROM product_images pi WHERE pi.product_id=p.id ORDER BY is_primary DESC LIMIT 1) AS image
             FROM wishlist_items wi JOIN products p ON p.id=wi.product_id WHERE wi.wishlist_id=:w ORDER BY wi.id DESC'
        );
        $st->execute(['w' => $wid]);
        return $st->fetchAll();
    }

    public static function remove(int $itemId): void
    {
        $wid = self::id();
        Database::connection()->prepare('DELETE FROM wishlist_items WHERE id=:id AND wishlist_id=:w')->execute(['id' => $itemId, 'w' => $wid]);
    }
}
