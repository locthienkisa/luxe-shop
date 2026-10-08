<?php

declare(strict_types=1);

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    public function code(): string;

    public function label(): string;

    /** @return array{status:string, txn_id:?string, payload:?array} */
    public function charge(array $order, array $payload = []): array;
}
