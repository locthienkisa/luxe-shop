<?php

declare(strict_types=1);

namespace App\Services\Payment;

final class PaymentManager
{
    /** @return array<string, PaymentGatewayInterface> */
    public static function enabled(): array
    {
        $all = [
            new CodGateway(),
            new VnPayGateway(),
            new MomoGateway(),
            new StripeGateway(),
        ];
        $out = [];
        foreach ($all as $g) {
            if ($g->code() === 'cod') {
                $out[$g->code()] = $g;
            }
        }
        return $out;
    }

    public static function get(string $code): PaymentGatewayInterface
    {
        $map = self::enabled();
        if (!isset($map[$code])) {
            throw new \RuntimeException('Phương thức thanh toán không hỗ trợ.');
        }
        return $map[$code];
    }
}
