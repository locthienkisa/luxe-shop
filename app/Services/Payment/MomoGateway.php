<?php

declare(strict_types=1);

namespace App\Services\Payment;

final class MomoGateway implements PaymentGatewayInterface
{
    public function code(): string
    {
        return 'momo';
    }

    public function label(): string
    {
        return 'MoMo';
    }

    public function charge(array $order, array $payload = []): array
    {
        throw new \RuntimeException('Cổng MoMo chưa được kích hoạt.');
    }
}
