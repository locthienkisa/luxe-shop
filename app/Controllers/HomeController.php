<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;

final class HomeController
{
    public function index(Request $r, array $p): void
    {
        View::render('home/index', [
            'title' => setting('site_name', config('app.name')),
            'meta_description' => setting('meta_description', 'Mua sắm hiện đại — giao nhanh toàn quốc.'),
            'heroes' => Banner::active('hero'),
            'promos' => Banner::active('promo'),
            'categories' => Category::active(),
            'featured' => Product::collection('featured', 8),
            'sale' => Product::collection('sale', 8),
            'best' => Product::collection('best', 8),
            'new' => Product::collection('new', 8),
            'reviews' => Review::latestApproved(6),
        ]);
    }

    public function sitemap(Request $r, array $p): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        $base = rtrim((string) config('app.url'), '/');
        echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        echo '<url><loc>' . e($base . '/') . '</loc></url>';
        echo '<url><loc>' . e($base . '/san-pham') . '</loc></url>';
        foreach (Category::active() as $c) {
            echo '<url><loc>' . e($base . '/danh-muc/' . $c['slug']) . '</loc></url>';
        }
        $products = Product::search([], 0, 500)['rows'];
        foreach ($products as $p) {
            echo '<url><loc>' . e($base . '/san-pham/' . $p['slug']) . '</loc></url>';
        }
        echo '</urlset>';
        exit;
    }
}
