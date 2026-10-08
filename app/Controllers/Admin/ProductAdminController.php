<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;
use App\Core\View;
use App\Models\AdminLog;
use App\Models\Category;
use App\Models\Product;

final class ProductAdminController
{
    public function index(Request $r, array $p): void
    {
        $page = max(1, (int) ($r->query['page'] ?? 1));
        $filters = [
            'q' => $r->query['q'] ?? '',
            'brand' => $r->query['brand'] ?? '',
            'category' => $r->query['category'] ?? '',
            'sort' => $r->query['sort'] ?? 'newest',
        ];
        if (isset($r->query['active'])) {
            // admin sees all: temporarily reuse search without is_active-only? search forces is_active=1
        }
        $res = $this->searchAll($filters, ($page - 1) * 20, 20);
        View::render('admin/products/index', [
            'title' => 'Sản phẩm',
            'products' => $res['rows'],
            'meta' => paginate_meta($res['total'], $page, 20),
            'filters' => $filters,
            'categories' => Category::all('sort_order ASC'),
        ], 'layouts/admin');
    }

    private function searchAll(array $f, int $off, int $lim): array
    {
        $where = ['1=1'];
        $params = [];
        if (!empty($f['q'])) {
            $where[] = '(p.name LIKE :q OR p.sku LIKE :q)';
            $params['q'] = '%' . $f['q'] . '%';
        }
        $w = implode(' AND ', $where);
        $c = Product::db()->prepare("SELECT COUNT(*) FROM products p WHERE $w");
        $c->execute($params);
        $total = (int) $c->fetchColumn();
        $st = Product::db()->prepare(
            "SELECT p.*, (SELECT path FROM product_images pi WHERE pi.product_id=p.id ORDER BY is_primary DESC LIMIT 1) image
             FROM products p WHERE $w ORDER BY p.id DESC LIMIT $lim OFFSET $off"
        );
        $st->execute($params);
        return ['total' => $total, 'rows' => $st->fetchAll()];
    }

    public function create(Request $r, array $p): void
    {
        View::render('admin/products/form', [
            'title' => 'Thêm sản phẩm',
            'product' => null,
            'categories' => Category::all('name ASC'),
            'selected' => [],
        ], 'layouts/admin');
    }

    public function store(Request $r, array $p): void
    {
        $this->save($r, null);
    }

    public function edit(Request $r, array $p): void
    {
        $product = Product::find((int) $p['id']);
        if (!$product) {
            \App\Core\Response::abort(404);
        }
        View::render('admin/products/form', [
            'title' => 'Sửa sản phẩm',
            'product' => $product,
            'categories' => Category::all('name ASC'),
            'selected' => array_column(Product::categoriesOf((int) $product['id']), 'id'),
            'images' => Product::images((int) $product['id']),
            'variants' => Product::variants((int) $product['id']),
        ], 'layouts/admin');
    }

    public function update(Request $r, array $p): void
    {
        $this->save($r, (int) $p['id']);
    }

    private function save(Request $r, ?int $id): void
    {
        $errors = Validator::validate($r->post, ['name' => 'required|max:200', 'sku' => 'required', 'price' => 'required|numeric']);
        if ($errors) {
            Session::flash('errors', $errors);
            redirect($id ? url('/admin/san-pham/' . $id . '/sua') : url('/admin/san-pham/them'));
        }
        $slug = slugify((string) $r->input('slug') ?: (string) $r->input('name'));
        $data = [
            'name' => $r->input('name'),
            'slug' => $slug,
            'sku' => $r->input('sku'),
            'brand' => $r->input('brand'),
            'short_description' => $r->input('short_description'),
            'description' => $r->input('description'),
            'price' => (float) $r->input('price'),
            'sale_price' => $r->input('sale_price') !== '' ? (float) $r->input('sale_price') : null,
            'stock' => (int) $r->input('stock', 0),
            'has_variants' => $r->input('has_variants') ? 1 : 0,
            'is_featured' => $r->input('is_featured') ? 1 : 0,
            'is_active' => $r->input('is_active') ? 1 : 0,
            'meta_title' => $r->input('meta_title'),
            'meta_description' => $r->input('meta_description'),
        ];
        if ($id) {
            Product::updateRow($id, $data);
            AdminLog::record('product.update', 'product', $id);
        } else {
            $id = Product::create($data);
            AdminLog::record('product.create', 'product', $id);
        }
        $cats = $r->post['categories'] ?? [];
        if (!is_array($cats)) {
            $cats = [];
        }
        Product::syncCategories($id, $cats);

        if (!empty($r->files['image']['name'])) {
            try {
                $path = Uploader::image($r->files['image'], 'products');
                if ($path) {
                    Product::addImage($id, $path, true);
                }
            } catch (\Throwable $e) {
                Session::flash('error', $e->getMessage());
            }
        }
        Session::flash('success', 'Đã lưu sản phẩm.');
        redirect(url('/admin/san-pham'));
    }

    public function delete(Request $r, array $p): void
    {
        $id = (int) $r->input('id');
        Product::delete($id);
        AdminLog::record('product.delete', 'product', $id);
        Session::flash('success', 'Đã xóa sản phẩm.');
        redirect(url('/admin/san-pham'));
    }
}
