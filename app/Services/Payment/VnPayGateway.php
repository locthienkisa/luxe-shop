<?php

declare(strict_types=1);

namespace App\Services\Payment;

/**
 * Stub — cấu hình VNPAY_TMN_CODE / VNPAY_HASH_SECRET trong .env khi tích hợp.
 */
final class VnPayGateway implements PaymentGatewayInterface
{
    public function code(): string
    {
        return 'vnpay';
    }

    public function label(): string
    {
        return 'VNPay';
    }

    public function charge(array $order, array $payload = []): array
    {
        throw new \RuntimeException('Cổng VNPay chưa được kích hoạt. Điền khóa trong .env rồi triển khai redirect URL.');
    }
}
