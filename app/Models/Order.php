<?php

declare(strict_types=1);

namespace App\Models;

final class Order extends Model
{
    protected static string $table = 'orders';

    public static function findByNumber(string $number): ?array
    {
        $st = self::db()->prepare('SELECT * FROM orders WHERE order_number = :n');
        $st->execute(['n' => $number]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function owned(int $id, int $userId): ?array
    {
        $st = self::db()->prepare('SELECT * FROM orders WHERE id = :id AND user_id = :u');
        $st->execute(['id' => $id, 'u' => $userId]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function items(int $orderId): array
    {
        $st = self::db()->prepare('SELECT * FROM order_items WHERE order_id = :id');
        $st->execute(['id' => $orderId]);
        return $st->fetchAll();
    }

    public static function history(int $orderId): array
    {
        $st = self::db()->prepare('SELECT * FROM order_status_history WHERE order_id = :id ORDER BY id ASC');
        $st->execute(['id' => $orderId]);
        return $st->fetchAll();
    }

    public static function payments(int $orderId): array
    {
        $st = self::db()->prepare('SELECT * FROM payments WHERE order_id = :id ORDER BY id DESC');
        $st->execute(['id' => $orderId]);
        return $st->fetchAll();
    }

    public static function forUser(int $userId, int $offset, int $limit): array
    {
        $cst = self::db()->prepare('SELECT COUNT(*) FROM orders WHERE user_id = :u');
        $cst->execute(['u' => $userId]);
        $total = (int) $cst->fetchColumn();
        $st = self::db()->prepare('SELECT * FROM orders WHERE user_id = :u ORDER BY id DESC LIMIT ' . $limit . ' OFFSET ' . $offset);
        $st->execute(['u' => $userId]);
        return ['total' => $total, 'rows' => $st->fetchAll()];
    }

    public static function adminList(array $f, int $offset, int $limit): array
    {
        $where = ['1=1'];
        $params = [];
        if (!empty($f['q'])) {
            $where[] = '(o.order_number LIKE :q OR o.shipping_name LIKE :q OR o.shipping_phone LIKE :q)';
            $params['q'] = '%' . $f['q'] . '%';
        }
        if (!empty($f['status'])) {
            $where[] = 'o.status = :status';
            $params['status'] = $f['status'];
        }
        $w = implode(' AND ', $where);
        $c = self::db()->prepare("SELECT COUNT(*) FROM orders o WHERE $w");
        $c->execute($params);
        $total = (int) $c->fetchColumn();
        $st = self::db()->prepare("SELECT o.*, u.email FROM orders o JOIN users u ON u.id=o.user_id WHERE $w ORDER BY o.id DESC LIMIT $limit OFFSET $offset");
        $st->execute($params);
        return ['total' => $total, 'rows' => $st->fetchAll()];
    }

    public static function dashboardStats(): array
    {
        $db = self::db();
        $revenue = (float) $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status NOT IN ('cancelled','refunded')")->fetchColumn();
        $today = (float) $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE DATE(created_at)=CURDATE() AND status NOT IN ('cancelled','refunded')")->fetchColumn();
        $month = (float) $db->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE YEAR(created_at)=YEAR(CURDATE()) AND MONTH(created_at)=MONTH(CURDATE()) AND status NOT IN ('cancelled','refunded')")->fetchColumn();
        $orders = (int) $db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
        $processing = (int) $db->query("SELECT COUNT(*) FROM orders WHERE status IN ('pending','confirmed','processing','shipping')")->fetchColumn();
        $done = (int) $db->query("SELECT COUNT(*) FROM orders WHERE status='delivered'")->fetchColumn();
        $customers = (int) $db->query('SELECT COUNT(*) FROM users WHERE role_id = (SELECT id FROM roles WHERE slug=\'customer\')')->fetchColumn();
        $products = (int) $db->query('SELECT COUNT(*) FROM products')->fetchColumn();
        return compact('revenue', 'today', 'month', 'orders', 'processing', 'done', 'customers', 'products');
    }

    public static function revenueByDay(int $days = 14): array
    {
        $st = self::db()->prepare(
            "SELECT DATE(created_at) d, COALESCE(SUM(total),0) t FROM orders
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :n DAY) AND status NOT IN ('cancelled','refunded')
             GROUP BY DATE(created_at) ORDER BY d"
        );
        $st->execute(['n' => $days]);
        return $st->fetchAll();
    }

    public static function revenueByMonth(int $months = 6): array
    {
        $st = self::db()->query(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') m, COALESCE(SUM(total),0) t FROM orders
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) AND status NOT IN ('cancelled','refunded')
             GROUP BY DATE_FORMAT(created_at,'%Y-%m') ORDER BY m"
        );
        return $st->fetchAll();
    }

    public static function countByStatus(): array
    {
        return self::db()->query('SELECT status, COUNT(*) c FROM orders GROUP BY status')->fetchAll();
    }

    public static function topProducts(int $limit = 8): array
    {
        return self::db()->query(
            'SELECT oi.product_name, SUM(oi.quantity) qty, SUM(oi.line_total) revenue
             FROM order_items oi JOIN orders o ON o.id=oi.order_id
             WHERE o.status NOT IN (\'cancelled\',\'refunded\')
             GROUP BY oi.product_id, oi.product_name ORDER BY qty DESC LIMIT ' . (int) $limit
        )->fetchAll();
    }
}
