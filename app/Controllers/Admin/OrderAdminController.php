<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\Order;
use App\Services\OrderService;

final class OrderAdminController
{
    public function index(Request $r, array $p): void
    {
        $page = max(1, (int) ($r->query['page'] ?? 1));
        $f = ['q' => $r->query['q'] ?? '', 'status' => $r->query['status'] ?? ''];
        $res = Order::adminList($f, ($page - 1) * 20, 20);
        View::render('admin/orders/index', [
            'title' => 'Đơn hàng',
            'orders' => $res['rows'],
            'meta' => paginate_meta($res['total'], $page, 20),
            'filters' => $f,
        ], 'layouts/admin');
    }

    public function show(Request $r, array $p): void
    {
        $order = Order::find((int) $p['id']);
        if (!$order) {
            Response::abort(404);
        }
        View::render('admin/orders/show', [
            'title' => 'Đơn ' . $order['order_number'],
            'order' => $order,
            'items' => Order::items((int) $order['id']),
            'history' => Order::history((int) $order['id']),
            'payments' => Order::payments((int) $order['id']),
        ], 'layouts/admin');
    }

    public function status(Request $r, array $p): void
    {
        try {
            OrderService::changeStatus((int) $r->input('id'), (string) $r->input('status'), (string) $r->input('note', ''));
            Session::flash('success', 'Đã đổi trạng thái.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }
        redirect(url('/admin/don-hang/' . (int) $r->input('id')));
    }

    public function paid(Request $r, array $p): void
    {
        OrderService::markPaid((int) $r->input('id'));
        Session::flash('success', 'Đã xác nhận thanh toán.');
        redirect(url('/admin/don-hang/' . (int) $r->input('id')));
    }
}
