<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\View;
use App\Models\AdminLog;
use App\Models\Banner;

final class BannerAdminController
{
    public function index(Request $r, array $p): void
    {
        View::render('admin/banners/index', ['title' => 'Banner', 'banners' => Banner::all('sort_order ASC')], 'layouts/admin');
    }

    public function store(Request $r, array $p): void
    {
        try {
            $path = Uploader::image($r->files['image'] ?? [], 'banners');
        } catch (\Throwable $e) {
            Session::flash('error', $e->getMessage());
            redirect(url('/admin/banner'));
        }
        if (!$path) {
            Session::flash('error', 'Cần ảnh banner.');
            redirect(url('/admin/banner'));
        }
        $id = Banner::create([
            'title' => $r->input('title'),
            'subtitle' => $r->input('subtitle'),
            'image' => $path,
            'link' => $r->input('link'),
            'position' => in_array($r->input('position'), ['hero', 'promo', 'sidebar'], true) ? $r->input('position') : 'hero',
            'sort_order' => (int) $r->input('sort_order', 0),
            'is_active' => $r->input('is_active') ? 1 : 0,
            'starts_at' => $r->input('starts_at') ?: null,
            'ends_at' => $r->input('ends_at') ?: null,
        ]);
        AdminLog::record('banner.create', 'banner', $id);
        redirect(url('/admin/banner'));
    }

    public function delete(Request $r, array $p): void
    {
        Banner::delete((int) $r->input('id'));
        AdminLog::record('banner.delete', 'banner', (int) $r->input('id'));
        redirect(url('/admin/banner'));
    }
}
