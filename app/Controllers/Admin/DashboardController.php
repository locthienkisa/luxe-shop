<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\View;
use App\Models\Order;
use App\Models\Product;

final class DashboardController
{
    public function index(Request $r, array $p): void
    {
        View::render('admin/dashboard/index', [
            'title' => 'Bảng điều khiển',
            'stats' => Order::dashboardStats(),
            'low' => Product::lowStock(5),
            'days' => Order::revenueByDay(14),
            'months' => Order::revenueByMonth(),
            'byStatus' => Order::countByStatus(),
            'top' => Order::topProducts(),
        ], 'layouts/admin');
    }
}
