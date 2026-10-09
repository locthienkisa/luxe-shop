<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Database;
use App\Core\Session;
use App\Models\Coupon;
use App\Models\Product;

final class CartService
{
    public static function currentCartId(): int
    {
        $pdo = Database::connection();
        $userId = Auth::id();
        $sid = Session::id();

        if ($userId) {
            $st = $pdo->prepare('SELECT id FROM carts WHERE user_id = :u LIMIT 1');
            $st->execute(['u' => $userId]);
            $id = $st->fetchColumn();
            if ($id) {
                self::mergeGuest($userId, (int) $id);
                return (int) $id;
            }
            $pdo->prepare('INSERT INTO carts (user_id, session_id) VALUES (:u, :s)')->execute(['u' => $userId, 's' => $sid]);
            return (int) $pdo->lastInsertId();
        }

        $st = $pdo->prepare('SELECT id FROM carts WHERE session_id = :s AND user_id IS NULL LIMIT 1');
        $st->execute(['s' => $sid]);
        $id = $st->fetchColumn();
        if ($id) {
            return (int) $id;
        }
        $pdo->prepare('INSERT INTO carts (session_id) VALUES (:s)')->execute(['s' => $sid]);
        return (int) $pdo->lastInsertId();
    }

    private static function mergeGuest(int $userId, int $userCartId): void
    {
        $pdo = Database::connection();
        $sid = Session::id();
        $st = $pdo->prepare('SELECT id FROM carts WHERE session_id = :s AND user_id IS NULL');
        $st->execute(['s' => $sid]);
        $guestId = $st->fetchColumn();
        if (!$guestId || (int) $guestId === $userCartId) {
            return;
        }
        $items = $pdo->prepare('SELECT * FROM cart_items WHERE cart_id = :id');
        $items->execute(['id' => $guestId]);
        foreach ($items->fetchAll() as $item) {
            try {
                self::add((int) $item['product_id'], $item['variant_id'] ? (int) $item['variant_id'] : null, (int) $item['quantity'], $userCartId);
            } catch (\Throwable) {
            }
        }
        $pdo->prepare('DELETE FROM carts WHERE id = :id')->execute(['id' => $guestId]);
    }

    public static function add(int $productId, ?int $variantId, int $qty, ?int $cartId = null): void
    {
        if ($qty < 1) {
            throw new \InvalidArgumentException('Số lượng không hợp lệ.');
        }
        $product = Product::find($productId);
        if (!$product || !(int) $product['is_active']) {
            throw new \RuntimeException('Sản phẩm không tồn tại.');
        }
        $stock = Product::availableStock($productId, $variantId);
        $cartId = $cartId ?? self::currentCartId();
        $pdo = Database::connection();

        if ($variantId) {
            $st = $pdo->prepare('SELECT * FROM cart_items WHERE cart_id=:c AND product_id=:p AND variant_id=:v');
            $st->execute(['c' => $cartId, 'p' => $productId, 'v' => $variantId]);
        } else {
            $st = $pdo->prepare('SELECT * FROM cart_items WHERE cart_id=:c AND product_id=:p AND variant_id IS NULL');
            $st->execute(['c' => $cartId, 'p' => $productId]);
        }
        $existing = $st->fetch();
        $newQty = $qty + (int) ($existing['quantity'] ?? 0);
        if ($newQty > $stock) {
            throw new \RuntimeException('Số lượng vượt quá tồn kho (' . $stock . ').');
        }
        if ($existing) {
            $pdo->prepare('UPDATE cart_items SET quantity=:q WHERE id=:id')->execute(['q' => $newQty, 'id' => $existing['id']]);
        } else {
            $pdo->prepare(
                'INSERT INTO cart_items (cart_id, product_id, variant_id, quantity) VALUES (:c,:p,:v,:q)'
            )->execute(['c' => $cartId, 'p' => $productId, 'v' => $variantId, 'q' => $qty]);
        }
    }

    public static function updateQty(int $itemId, int $qty): void
    {
        $cartId = self::currentCartId();
        $pdo = Database::connection();
        $st = $pdo->prepare('SELECT * FROM cart_items WHERE id=:id AND cart_id=:c');
        $st->execute(['id' => $itemId, 'c' => $cartId]);
        $item = $st->fetch();
        if (!$item) {
            throw new \RuntimeException('Mục giỏ hàng không tồn tại.');
        }
        if ($qty < 1) {
            self::remove($itemId);
            return;
        }
        $stock = Product::availableStock((int) $item['product_id'], $item['variant_id'] ? (int) $item['variant_id'] : null);
        if ($qty > $stock) {
            throw new \RuntimeException('Số lượng vượt quá tồn kho.');
        }
        $pdo->prepare('UPDATE cart_items SET quantity=:q WHERE id=:id AND cart_id=:c')->execute(['q' => $qty, 'id' => $itemId, 'c' => $cartId]);
    }

    public static function remove(int $itemId): void
    {
        $cartId = self::currentCartId();
        Database::connection()->prepare('DELETE FROM cart_items WHERE id=:id AND cart_id=:c')->execute(['id' => $itemId, 'c' => $cartId]);
    }

    public static function clear(?int $cartId = null): void
    {
        $cartId = $cartId ?? self::currentCartId();
        Database::connection()->prepare('DELETE FROM cart_items WHERE cart_id=:c')->execute(['c' => $cartId]);
        Database::connection()->prepare('UPDATE carts SET coupon_id=NULL WHERE id=:c')->execute(['c' => $cartId]);
    }

    public static function items(): array
    {
        $cartId = self::currentCartId();
        $sql = 'SELECT ci.*, p.name, p.slug, p.sku, p.price, p.sale_price, p.is_active AS product_active, p.stock AS product_stock,
                       v.sku AS variant_sku, v.size, v.color, v.price AS variant_price, v.stock AS variant_stock,
                       (SELECT path FROM product_images pi WHERE pi.product_id=p.id ORDER BY is_primary DESC LIMIT 1) AS image
                FROM cart_items ci
                JOIN products p ON p.id = ci.product_id
                LEFT JOIN product_variants v ON v.id = ci.variant_id
                WHERE ci.cart_id = :c';
        $st = Database::connection()->prepare($sql);
        $st->execute(['c' => $cartId]);
        $rows = $st->fetchAll();
        foreach ($rows as &$r) {
            $base = $r['variant_price'] !== null ? (float) $r['variant_price'] : product_price($r);
            $r['unit_price'] = $base;
            $r['line_total'] = $base * (int) $r['quantity'];
            $r['stock'] = $r['variant_id'] ? (int) $r['variant_stock'] : (int) $r['product_stock'];
        }
        return $rows;
    }

    public static function totals(): array
    {
        $items = self::items();
        $subtotal = 0.0;
        foreach ($items as $i) {
            $subtotal += $i['line_total'];
        }
        $discount = 0.0;
        $coupon = self::appliedCoupon();
        if ($coupon) {
            $discount = CouponService::compute($coupon, $subtotal);
        }
        $shipCfg = (float) Config::get('app.shipping_fee', 30000);
        $freeMin = (float) Config::get('app.free_shipping_min', 500000);
        $after = max(0, $subtotal - $discount);
        $shipping = $after >= $freeMin ? 0.0 : $shipCfg;
        $total = $after + $shipping;
        return compact('items', 'subtotal', 'discount', 'shipping', 'total', 'coupon');
    }

    public static function count(): int
    {
        try {
            $cartId = self::currentCartId();
            $st = Database::connection()->prepare('SELECT COALESCE(SUM(quantity),0) FROM cart_items WHERE cart_id=:c');
            $st->execute(['c' => $cartId]);
            return (int) $st->fetchColumn();
        } catch (\Throwable) {
            return 0;
        }
    }

    public static function applyCoupon(?int $couponId): void
    {
        $cartId = self::currentCartId();
        Database::connection()->prepare('UPDATE carts SET coupon_id=:cp WHERE id=:c')->execute(['cp' => $couponId, 'c' => $cartId]);
    }

    public static function appliedCoupon(): ?array
    {
        $cartId = self::currentCartId();
        $st = Database::connection()->prepare('SELECT coupon_id FROM carts WHERE id=:c');
        $st->execute(['c' => $cartId]);
        $id = $st->fetchColumn();
        if (!$id) {
            return null;
        }
        return Coupon::find((int) $id);
    }
}
