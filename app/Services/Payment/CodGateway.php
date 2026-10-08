<?php

declare(strict_types=1);

namespace App\Services\Payment;

final class CodGateway implements PaymentGatewayInterface
{
    public function code(): string
    {
        return 'cod';
    }

    public function label(): string
    {
        return 'Thanh toán khi nhận hàng (COD)';
    }

    public function charge(array $order, array $payload = []): array
    {
        return ['status' => 'pending', 'txn_id' => null, 'payload' => ['note' => 'COD — thu hộ khi giao']];
    }
}
