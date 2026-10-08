<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Models\AdminLog;
use App\Models\Product;
use App\Models\Review;

final class ReviewAdminController
{
    public function index(Request $r, array $p): void
    {
        $status = (string) ($r->query['status'] ?? '');
        $page = max(1, (int) ($r->query['page'] ?? 1));
        $res = Review::adminList($status, ($page - 1) * 20, 20);
        View::render('admin/reviews/index', [
            'title' => 'Đánh giá',
            'reviews' => $res['rows'],
            'meta' => paginate_meta($res['total'], $page, 20),
            'status' => $status,
        ], 'layouts/admin');
    }

    public function moderate(Request $r, array $p): void
    {
        $id = (int) $r->input('id');
        $status = (string) $r->input('status');
        if (!in_array($status, ['approved', 'hidden', 'pending'], true)) {
            Session::flash('error', 'Trạng thái không hợp lệ.');
            redirect(url('/admin/danh-gia'));
        }
        $rev = Review::find($id);
        Database::connection()->prepare('UPDATE reviews SET status=:s WHERE id=:id')->execute(['s' => $status, 'id' => $id]);
        if ($rev) {
            Product::recountRating((int) $rev['product_id']);
        }
        AdminLog::record('review.moderate', 'review', $id, ['status' => $status]);
        redirect(url('/admin/danh-gia'));
    }

    public function delete(Request $r, array $p): void
    {
        $id = (int) $r->input('id');
        $rev = Review::find($id);
        Database::connection()->prepare('DELETE FROM reviews WHERE id=:id')->execute(['id' => $id]);
        if ($rev) {
            Product::recountRating((int) $rev['product_id']);
        }
        AdminLog::record('review.delete', 'review', $id);
        redirect(url('/admin/danh-gia'));
    }
}
