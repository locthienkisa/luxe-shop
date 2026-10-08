<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Models\Address;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\Payment\PaymentManager;

final class CheckoutController
{
    public function index(Request $r, array $p): void
    {
        $totals = CartService::totals();
        if (!$totals['items']) {
            Session::flash('error', 'Giỏ hàng trống.');
            redirect(url('/gio-hang'));
        }
        View::render('checkout/index', [
            'title' => 'Thanh toán',
            'addresses' => Address::forUser(Auth::id()),
            'gateways' => PaymentManager::enabled(),
            'user' => Auth::user(),
        ] + $totals);
    }

    public function place(Request $r, array $p): void
    {
        try {
            $order = CheckoutService::place($r->post);
            Session::flash('success', 'Đặt hàng thành công.');
            redirect(url('/don-hang/' . $order['id']));
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            redirect(url('/thanh-toan'));
        }
    }
}
