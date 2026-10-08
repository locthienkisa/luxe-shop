<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Models\Address;
use App\Models\AdminLog;
use App\Models\Product;
use App\Services\Payment\PaymentManager;

final class CheckoutService
{
    public static function place(array $input): array
    {
        $userId = Auth::id();
        if (!$userId) {
            throw new \RuntimeException('Cần đăng nhập để đặt hàng.');
        }
        $totals = CartService::totals();
        if (!$totals['items']) {
            throw new \RuntimeException('Giỏ hàng trống.');
        }

        foreach ($totals['items'] as $item) {
            $stock = Product::availableStock((int) $item['product_id'], $item['variant_id'] ? (int) $item['variant_id'] : null);
            if ((int) $item['quantity'] > $stock) {
                throw new \RuntimeException('Sản phẩm ' . $item['name'] . ' không đủ tồn kho.');
            }
        }

        $method = (string) ($input['payment_method'] ?? 'cod');
        $gateway = PaymentManager::get($method);

        $address = null;
        if (!empty($input['address_id'])) {
            $address = Address::owned((int) $input['address_id'], $userId);
            if (!$address) {
                throw new \RuntimeException('Địa chỉ không hợp lệ.');
            }
        }

        $name = trim((string) ($address['full_name'] ?? $input['shipping_name'] ?? ''));
        $phone = trim((string) ($address['phone'] ?? $input['shipping_phone'] ?? ''));
        $addr = $address ? Address::format($address) : trim((string) ($input['shipping_address'] ?? ''));
        if ($name === '' || $phone === '' || $addr === '') {
            throw new \RuntimeException('Thiếu thông tin giao hàng.');
        }

        if ($totals['coupon']) {
            CouponService::validate($totals['coupon'], $totals['subtotal'], $userId);
        }

        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            $orderNumber = 'LX' . date('ymd') . strtoupper(bin2hex(random_bytes(3)));
            $pdo->prepare(
                'INSERT INTO orders (order_number,user_id,address_id,shipping_name,shipping_phone,shipping_address,
                 subtotal,discount,shipping_fee,total,coupon_id,coupon_code,status,payment_status,payment_method,notes)
                 VALUES (:n,:u,:a,:sn,:sp,:sa,:sub,:disc,:ship,:tot,:cid,:ccode,\'pending\',\'unpaid\',:pm,:notes)'
            )->execute([
                'n' => $orderNumber,
                'u' => $userId,
                'a' => $address['id'] ?? null,
                'sn' => $name,
                'sp' => $phone,
                'sa' => $addr,
                'sub' => $totals['subtotal'],
                'disc' => $totals['discount'],
                'ship' => $totals['shipping'],
                'tot' => $totals['total'],
                'cid' => $totals['coupon']['id'] ?? null,
                'ccode' => $totals['coupon']['code'] ?? null,
                'pm' => $method,
                'notes' => substr((string) ($input['notes'] ?? ''), 0, 500),
            ]);
            $orderId = (int) $pdo->lastInsertId();

            $itemSt = $pdo->prepare(
                'INSERT INTO order_items (order_id,product_id,variant_id,product_name,sku,size,color,unit_price,quantity,line_total)
                 VALUES (:oid,:pid,:vid,:name,:sku,:size,:color,:price,:qty,:lt)'
            );
            foreach ($totals['items'] as $item) {
                $sku = $item['variant_sku'] ?: $item['sku'];
                $itemSt->execute([
                    'oid' => $orderId,
                    'pid' => $item['product_id'],
                    'vid' => $item['variant_id'] ?: null,
                    'name' => $item['name'],
                    'sku' => $sku,
                    'size' => $item['size'] ?? null,
                    'color' => $item['color'] ?? null,
                    'price' => $item['unit_price'],
                    'qty' => $item['quantity'],
                    'lt' => $item['line_total'],
                ]);
                Product::adjustStock((int) $item['product_id'], $item['variant_id'] ? (int) $item['variant_id'] : null, -1 * (int) $item['quantity']);
                $pdo->prepare(
                    'INSERT INTO inventory (product_id,variant_id,change_qty,reason,reference_type,reference_id,created_by)
                     VALUES (:p,:v,:q,\'sale\',\'order\',:oid,:uid)'
                )->execute([
                    'p' => $item['product_id'],
                    'v' => $item['variant_id'] ?: null,
                    'q' => -1 * (int) $item['quantity'],
                    'oid' => $orderId,
                    'uid' => $userId,
                ]);
                $pdo->prepare('UPDATE products SET sold_count = sold_count + :q WHERE id=:id')->execute(['q' => $item['quantity'], 'id' => $item['product_id']]);
            }

            $order = ['id' => $orderId, 'order_number' => $orderNumber, 'total' => $totals['total']];
            $charge = $gateway->charge($order, $input);
            $pdo->prepare(
                'INSERT INTO payments (order_id,method,amount,status,gateway_txn_id,payload_json)
                 VALUES (:oid,:m,:amt,:st,:txn,:pl)'
            )->execute([
                'oid' => $orderId,
                'm' => $method,
                'amt' => $totals['total'],
                'st' => $charge['status'],
                'txn' => $charge['txn_id'],
                'pl' => $charge['payload'] ? json_encode($charge['payload']) : null,
            ]);

            $pdo->prepare(
                'INSERT INTO order_status_history (order_id, from_status, to_status, note, created_by)
                 VALUES (:oid, NULL, \'pending\', \'Đặt hàng\', :uid)'
            )->execute(['oid' => $orderId, 'uid' => $userId]);

            if ($totals['coupon']) {
                $pdo->prepare('INSERT INTO coupon_usages (coupon_id,user_id,order_id) VALUES (:c,:u,:o)')
                    ->execute(['c' => $totals['coupon']['id'], 'u' => $userId, 'o' => $orderId]);
                $pdo->prepare('UPDATE coupons SET used_count = used_count + 1 WHERE id=:id')->execute(['id' => $totals['coupon']['id']]);
            }

            CartService::clear();
            NotificationService::send($userId, 'Đặt hàng thành công', 'Đơn ' . $orderNumber . ' đã được tạo.', url('/don-hang/' . $orderId));
            AdminLog::record('order.create', 'order', $orderId, ['number' => $orderNumber]);
            $pdo->commit();
            return ['id' => $orderId, 'order_number' => $orderNumber];
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
