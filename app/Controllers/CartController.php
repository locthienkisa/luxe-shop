<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Models\Coupon;
use App\Services\CartService;
use App\Services\CouponService;

final class CartController
{
    public function index(Request $r, array $p): void
    {
        View::render('cart/index', ['title' => 'Giỏ hàng'] + CartService::totals());
    }

    public function add(Request $r, array $p): void
    {
        try {
            CartService::add((int) $r->input('product_id'), $r->input('variant_id') ? (int) $r->input('variant_id') : null, max(1, (int) $r->input('quantity', 1)));
            Session::flash('success', 'Đã thêm vào giỏ hàng.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }
        back();
    }

    public function update(Request $r, array $p): void
    {
        try {
            CartService::updateQty((int) $r->input('item_id'), (int) $r->input('quantity'));
            Session::flash('success', 'Đã cập nhật giỏ hàng.');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
        }
        redirect(url('/gio-hang'));
    }

    public function remove(Request $r, array $p): void
    {
        CartService::remove((int) $r->input('item_id'));
        Session::flash('success', 'Đã xóa sản phẩm.');
        redirect(url('/gio-hang'));
    }

    public function clear(Request $r, array $p): void
    {
        CartService::clear();
        Session::flash('success', 'Đã xóa toàn bộ giỏ hàng.');
        redirect(url('/gio-hang'));
    }

    public function coupon(Request $r, array $p): void
    {
        $code = strtoupper(trim((string) $r->input('code')));
        $coupon = Coupon::findByCode($code);
        $totals = CartService::totals();
        try {
            if (!$coupon) {
                throw new \RuntimeException('Mã không tồn tại.');
            }
            CouponService::validate($coupon, $totals['subtotal'], \App\Core\Auth::id());
            CartService::applyCoupon((int) $coupon['id']);
            Session::flash('success', 'Áp dụng mã thành công.');
        } catch (\Throwable $e) {
            CartService::applyCoupon(null);
            Session::flash('error', $e->getMessage());
        }
        redirect(url('/gio-hang'));
    }
}
