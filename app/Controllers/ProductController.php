<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;

final class ProductController
{
    public function index(Request $r, array $p): void
    {
        $this->listing($r, $p, null);
    }

    public function category(Request $r, array $p): void
    {
        $cat = Category::findBySlug((string) ($p['slug'] ?? ''));
        if (!$cat) {
            \App\Core\Response::abort(404);
        }
        $this->listing($r, $p, $cat);
    }

    private function listing(Request $r, array $p, ?array $category): void
    {
        $page = max(1, (int) ($r->query['page'] ?? 1));
        $per = 12;
        $filters = [
            'q' => trim((string) ($r->query['q'] ?? '')),
            'brand' => $r->query['brand'] ?? '',
            'category' => $category['slug'] ?? ($r->query['category'] ?? ''),
            'min_price' => $r->query['min_price'] ?? '',
            'max_price' => $r->query['max_price'] ?? '',
            'rating' => $r->query['rating'] ?? '',
            'in_stock' => $r->query['in_stock'] ?? '',
            'sale' => $r->query['sale'] ?? '',
            'sort' => $r->query['sort'] ?? 'newest',
        ];
        $result = Product::search($filters, ($page - 1) * $per, $per);
        $meta = paginate_meta($result['total'], $page, $per);
        View::render('catalog/index', [
            'title' => $category['name'] ?? ($filters['q'] ? 'Tìm kiếm: ' . $filters['q'] : 'Sản phẩm'),
            'meta_description' => $category['meta_description'] ?? 'Danh sách sản phẩm Luxe Shop',
            'category' => $category,
            'products' => $result['rows'],
            'meta' => $meta,
            'filters' => $filters,
            'brands' => Product::brands(),
            'categories' => Category::active(),
        ]);
    }

    public function show(Request $r, array $p): void
    {
        $product = Product::findBySlug((string) ($p['slug'] ?? ''));
        if (!$product || !(int) $product['is_active']) {
            \App\Core\Response::abort(404);
        }
        $canReview = false;
        if (Auth::id() && Config::get('app.verified_purchase_only', true)) {
            $st = Database::connection()->prepare(
                "SELECT o.id FROM order_items oi JOIN orders o ON o.id=oi.order_id
                 WHERE oi.product_id=:p AND o.user_id=:u AND o.status='delivered' LIMIT 1"
            );
            $st->execute(['p' => $product['id'], 'u' => Auth::id()]);
            $canReview = (bool) $st->fetchColumn();
        } elseif (Auth::id()) {
            $canReview = true;
        }
        View::render('catalog/show', [
            'title' => $product['meta_title'] ?: $product['name'],
            'meta_description' => $product['meta_description'] ?: $product['short_description'],
            'og_image' => Product::primaryImage((int) $product['id']),
            'canonical' => url('/san-pham/' . $product['slug']),
            'product' => $product,
            'images' => Product::images((int) $product['id']),
            'variants' => Product::variants((int) $product['id']),
            'cats' => Product::categoriesOf((int) $product['id']),
            'reviews' => Review::approvedForProduct((int) $product['id']),
            'canReview' => $canReview,
            'related' => Product::collection('featured', 4),
        ]);
    }
}
