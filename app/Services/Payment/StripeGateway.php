<?php

declare(strict_types=1);

namespace App\Services\Payment;

final class StripeGateway implements PaymentGatewayInterface
{
    public function code(): string
    {
        return 'stripe';
    }

    public function label(): string
    {
        return 'Stripe';
    }

    public function charge(array $order, array $payload = []): array
    {
        throw new \RuntimeException('Cổng Stripe chưa được kích hoạt.');
    }
}
