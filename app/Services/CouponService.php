<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Models\Coupon;

final class CouponService
{
    public static function validate(array $coupon, float $subtotal, ?int $userId): void
    {
        if (!(int) $coupon['is_active']) {
            throw new \RuntimeException('Mã giảm giá không còn hiệu lực.');
        }
        $now = time();
        if ($now < strtotime((string) $coupon['starts_at']) || $now > strtotime((string) $coupon['ends_at'])) {
            throw new \RuntimeException('Mã giảm giá hết hạn hoặc chưa bắt đầu.');
        }
        if ($coupon['usage_limit'] !== null && (int) $coupon['used_count'] >= (int) $coupon['usage_limit']) {
            throw new \RuntimeException('Mã giảm giá đã hết lượt sử dụng.');
        }
        if ($subtotal < (float) $coupon['min_order']) {
            throw new \RuntimeException('Đơn hàng chưa đạt giá trị tối thiểu.');
        }
        if ($userId) {
            $used = Coupon::userUsage((int) $coupon['id'], $userId);
            if ($used >= (int) $coupon['per_user_limit']) {
                throw new \RuntimeException('Bạn đã dùng hết lượt mã này.');
            }
        }
    }

    public static function compute(array $coupon, float $subtotal): float
    {
        try {
            self::validate($coupon, $subtotal, Auth::id());
        } catch (\Throwable) {
            return 0.0;
        }
        if ($coupon['discount_type'] === 'percent') {
            $d = $subtotal * ((float) $coupon['discount_value'] / 100);
            if ($coupon['max_discount'] !== null) {
                $d = min($d, (float) $coupon['max_discount']);
            }
            return round($d, 2);
        }
        return min($subtotal, (float) $coupon['discount_value']);
    }
}
