<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Services\WishlistService;

final class WishlistController
{
    public function index(Request $r, array $p): void
    {
        View::render('account/wishlist', ['title' => 'Yêu thích', 'items' => WishlistService::items()]);
    }

    public function toggle(Request $r, array $p): void
    {
        $on = WishlistService::toggle((int) $r->input('product_id'));
        Session::flash('success', $on ? 'Đã thêm vào yêu thích.' : 'Đã bỏ khỏi yêu thích.');
        back();
    }

    public function remove(Request $r, array $p): void
    {
        WishlistService::remove((int) $r->input('item_id'));
        redirect(url('/yeu-thich'));
    }
}
