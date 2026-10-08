<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Models\AdminLog;
use App\Models\Product;

final class InventoryAdminController
{
    public function index(Request $r, array $p): void
    {
        $low = Product::lowStock(10);
        $logs = Database::connection()->query(
            'SELECT i.*, p.name FROM inventory i JOIN products p ON p.id=i.product_id ORDER BY i.id DESC LIMIT 100'
        )->fetchAll();
        View::render('admin/inventory/index', ['title' => 'Tồn kho', 'low' => $low, 'logs' => $logs], 'layouts/admin');
    }

    public function adjust(Request $r, array $p): void
    {
        $pid = (int) $r->input('product_id');
        $qty = (int) $r->input('change_qty');
        $note = substr((string) $r->input('note'), 0, 255);
        if ($qty === 0) {
            Session::flash('error', 'Số lượng điều chỉnh khác 0.');
            redirect(url('/admin/ton-kho'));
        }
        $pdo = Database::connection();
        $pdo->beginTransaction();
        try {
            Product::adjustStock($pid, null, $qty);
            $pdo->prepare(
                'INSERT INTO inventory (product_id, change_qty, reason, note, created_by) VALUES (:p,:q,\'adjust\',:n,:u)'
            )->execute(['p' => $pid, 'q' => $qty, 'n' => $note, 'u' => Auth::id()]);
            AdminLog::record('inventory.adjust', 'product', $pid, ['qty' => $qty]);
            $pdo->commit();
            Session::flash('success', 'Đã điều chỉnh tồn kho.');
        } catch (\Throwable $e) {
            $pdo->rollBack();
            Session::flash('error', $e->getMessage());
        }
        redirect(url('/admin/ton-kho'));
    }
}
