<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Models\AdminLog;
use App\Models\Category;

final class CategoryAdminController
{
    public function index(Request $r, array $p): void
    {
        View::render('admin/categories/index', ['title' => 'Danh mục', 'categories' => Category::all('sort_order ASC')], 'layouts/admin');
    }

    public function store(Request $r, array $p): void
    {
        if (trim((string) $r->input('name')) === '') {
            Session::flash('error', 'Tên danh mục bắt buộc.');
            redirect(url('/admin/danh-muc'));
        }
        $id = Category::create([
            'parent_id' => $r->input('parent_id') ?: null,
            'name' => $r->input('name'),
            'slug' => slugify((string) $r->input('slug') ?: (string) $r->input('name')),
            'description' => $r->input('description'),
            'image' => null,
            'sort_order' => (int) $r->input('sort_order', 0),
            'is_active' => $r->input('is_active') ? 1 : 0,
            'meta_title' => $r->input('meta_title'),
            'meta_description' => $r->input('meta_description'),
        ]);
        AdminLog::record('category.create', 'category', $id);
        Session::flash('success', 'Đã tạo danh mục.');
        redirect(url('/admin/danh-muc'));
    }

    public function update(Request $r, array $p): void
    {
        $id = (int) $p['id'];
        Category::updateRow($id, [
            'parent_id' => $r->input('parent_id') ?: null,
            'name' => $r->input('name'),
            'slug' => slugify((string) $r->input('slug') ?: (string) $r->input('name')),
            'description' => $r->input('description'),
            'image' => $r->input('image'),
            'sort_order' => (int) $r->input('sort_order', 0),
            'is_active' => $r->input('is_active') ? 1 : 0,
            'meta_title' => $r->input('meta_title'),
            'meta_description' => $r->input('meta_description'),
        ]);
        AdminLog::record('category.update', 'category', $id);
        Session::flash('success', 'Đã cập nhật.');
        redirect(url('/admin/danh-muc'));
    }

    public function delete(Request $r, array $p): void
    {
        $id = (int) $r->input('id');
        Category::delete($id);
        AdminLog::record('category.delete', 'category', $id);
        redirect(url('/admin/danh-muc'));
    }
}
