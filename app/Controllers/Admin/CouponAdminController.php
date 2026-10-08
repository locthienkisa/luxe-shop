<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Models\AdminLog;
use App\Models\Coupon;

final class CouponAdminController
{
    public function index(Request $r, array $p): void
    {
        View::render('admin/coupons/index', ['title' => 'Mã giảm giá', 'coupons' => Coupon::all()], 'layouts/admin');
    }

    public function store(Request $r, array $p): void
    {
        $code = strtoupper(trim((string) $r->input('code')));
        if ($code === '') {
            Session::flash('error', 'Mã bắt buộc.');
            redirect(url('/admin/voucher'));
        }
        $id = Coupon::create([
            'code' => $code,
            'discount_type' => $r->input('discount_type') === 'percent' ? 'percent' : 'fixed',
            'discount_value' => (float) $r->input('discount_value'),
            'min_order' => (float) $r->input('min_order', 0),
            'max_discount' => $r->input('max_discount') !== '' ? (float) $r->input('max_discount') : null,
            'starts_at' => $r->input('starts_at'),
            'ends_at' => $r->input('ends_at'),
            'usage_limit' => $r->input('usage_limit') !== '' ? (int) $r->input('usage_limit') : null,
            'per_user_limit' => (int) $r->input('per_user_limit', 1),
            'is_active' => $r->input('is_active') ? 1 : 0,
        ]);
        AdminLog::record('coupon.create', 'coupon', $id);
        Session::flash('success', 'Đã tạo voucher.');
        redirect(url('/admin/voucher'));
    }

    public function update(Request $r, array $p): void
    {
        $id = (int) $p['id'];
        Coupon::updateRow($id, [
            'code' => strtoupper(trim((string) $r->input('code'))),
            'discount_type' => $r->input('discount_type') === 'percent' ? 'percent' : 'fixed',
            'discount_value' => (float) $r->input('discount_value'),
            'min_order' => (float) $r->input('min_order', 0),
            'max_discount' => $r->input('max_discount') !== '' ? (float) $r->input('max_discount') : null,
            'starts_at' => $r->input('starts_at'),
            'ends_at' => $r->input('ends_at'),
            'usage_limit' => $r->input('usage_limit') !== '' ? (int) $r->input('usage_limit') : null,
            'per_user_limit' => (int) $r->input('per_user_limit', 1),
            'is_active' => $r->input('is_active') ? 1 : 0,
        ]);
        AdminLog::record('coupon.update', 'coupon', $id);
        redirect(url('/admin/voucher'));
    }

    public function delete(Request $r, array $p): void
    {
        Coupon::delete((int) $r->input('id'));
        AdminLog::record('coupon.delete', 'coupon', (int) $r->input('id'));
        redirect(url('/admin/voucher'));
    }
}
