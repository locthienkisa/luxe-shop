<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Models\AdminLog;
use App\Models\Order;
use App\Models\Product;

final class OrderService
{
    private const FLOW = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['processing', 'cancelled'],
        'processing' => ['shipping', 'cancelled'],
        'shipping' => ['delivered', 'cancelled'],
        'delivered' => ['refunded'],
        'cancelled' => [],
        'refunded' => [],
    ];

    public static function changeStatus(int $orderId, string $to, string $note = '', bool $asUser = false): void
    {
        $order = Order::find($orderId);
        if (!$order) {
            throw new \RuntimeException('Đơn hàng không tồn tại.');
        }
        $from = $order['status'];
        if ($asUser) {
            if ($from !== 'pending' && $from !== 'confirmed') {
                throw new \RuntimeException('Không thể hủy đơn ở trạng thái hiện tại.');
            }
            $to = 'cancelled';
        }
        $allowed = self::FLOW[$from] ?? [];
        if (!in_array($to, $allowed, true)) {
            throw new \RuntimeException('Chuyển trạng thái không hợp lệ.');
        }

        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $pdo->prepare('UPDATE orders SET status=:s, cancelled_at = IF(:s2=\'cancelled\', NOW(), cancelled_at) WHERE id=:id')
                ->execute(['s' => $to, 's2' => $to, 'id' => $orderId]);
            $pdo->prepare(
                'INSERT INTO order_status_history (order_id,from_status,to_status,note,created_by) VALUES (:o,:f,:t,:n,:u)'
            )->execute(['o' => $orderId, 'f' => $from, 't' => $to, 'n' => $note, 'u' => Auth::id()]);

            if ($to === 'cancelled' || $to === 'refunded') {
                foreach (Order::items($orderId) as $item) {
                    Product::adjustStock((int) $item['product_id'], $item['variant_id'] ? (int) $item['variant_id'] : null, (int) $item['quantity']);
                    $pdo->prepare(
                        'INSERT INTO inventory (product_id,variant_id,change_qty,reason,reference_type,reference_id,created_by,note)
                         VALUES (:p,:v,:q,:r,\'order\',:oid,:uid,:note)'
                    )->execute([
                        'p' => $item['product_id'],
                        'v' => $item['variant_id'] ?: null,
                        'q' => $item['quantity'],
                        'r' => $to === 'refunded' ? 'return' : 'cancel_restore',
                        'oid' => $orderId,
                        'uid' => Auth::id(),
                        'note' => $note,
                    ]);
                    $pdo->prepare('UPDATE products SET sold_count = GREATEST(0, sold_count - :q) WHERE id=:id')
                        ->execute(['q' => $item['quantity'], 'id' => $item['product_id']]);
                }
            }
            if ($to === 'refunded') {
                $pdo->prepare("UPDATE orders SET payment_status='refunded' WHERE id=:id")->execute(['id' => $orderId]);
                $pdo->prepare("UPDATE payments SET status='refunded' WHERE order_id=:id")->execute(['id' => $orderId]);
            }

            NotificationService::send((int) $order['user_id'], 'Cập nhật đơn hàng', 'Đơn ' . $order['order_number'] . ' → ' . order_status_label($to), url('/don-hang/' . $orderId));
            AdminLog::record('order.status', 'order', $orderId, ['from' => $from, 'to' => $to]);
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function markPaid(int $orderId): void
    {
        $pdo = Database::connection();
        $pdo->prepare("UPDATE orders SET payment_status='paid' WHERE id=:id")->execute(['id' => $orderId]);
        $pdo->prepare("UPDATE payments SET status='paid' WHERE order_id=:id")->execute(['id' => $orderId]);
        AdminLog::record('order.paid', 'order', $orderId);
    }
}
