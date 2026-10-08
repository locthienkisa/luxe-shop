<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\Order;
use App\Services\OrderService;

final class OrderController
{
    public function index(Request $r, array $p): void
    {
        $page = max(1, (int) ($r->query['page'] ?? 1));
        $per = 10;
        $data = Order::forUser(Auth::id(), ($page - 1) * $per, $per);
        View::render('orders/index', [
            'title' => 'Đơn hàng của tôi',
            'orders' => $data['rows'],
            'meta' => paginate_meta($data['total'], $page, $per),
        ]);
    }

    public function show(Request $r, array $p): void
    {
        $order = Order::owned((int) $p['id'], Auth::id());
        if (!$order) {
            Response::abort(404);
        }
        View::render('orders/show', [
            'title' => 'Đơn ' . $order['order_number'],
            'order' => $order,
            'items' => Order::items((int) $order['id']),
            'history' => Order::history((int) $order['id']),
            'payments' => Order::payments((int) $order['id']),
        ]);
    }

    public function cancel(Request $r, array $p): void
    {
        $order = Order::owned((int) $r->input('id'), Auth::id());
        if (!$order) {
            Response::abort(403);
        }
        try {
            OrderService::changeStatus((int) $order['id'], 'cancelled', 'Khách hủy đơn', true);
            Session::flash('success', 'Đã hủy đơn hàng.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }
        redirect(url('/don-hang/' . $order['id']));
    }
}
