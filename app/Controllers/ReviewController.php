<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;
use App\Models\Product;
use App\Models\Review;

final class ReviewController
{
    public function store(Request $r, array $p): void
    {
        $errors = Validator::validate($r->post, [
            'product_id' => 'required|integer',
            'rating' => 'required|integer',
            'content' => 'required|min:5|max:2000',
        ]);
        $rating = (int) $r->input('rating');
        if ($rating < 1 || $rating > 5) {
            $errors['rating'] = 'Điểm từ 1 đến 5.';
        }
        $productId = (int) $r->input('product_id');
        $product = Product::find($productId);
        if (!$product) {
            Response::abort(404);
        }

        $orderId = null;
        if (Config::get('app.verified_purchase_only', true)) {
            $st = Database::connection()->prepare(
                "SELECT o.id FROM order_items oi JOIN orders o ON o.id=oi.order_id
                 WHERE oi.product_id=:p AND o.user_id=:u AND o.status='delivered' LIMIT 1"
            );
            $st->execute(['p' => $productId, 'u' => Auth::id()]);
            $orderId = $st->fetchColumn();
            if (!$orderId) {
                Session::flash('error', 'Chỉ khách đã mua và nhận hàng mới được đánh giá.');
                redirect(url('/san-pham/' . $product['slug']));
            }
        }

        if ($errors) {
            Session::flash('errors', $errors);
            redirect(url('/san-pham/' . $product['slug']));
        }

        $pdo = Database::connection();
        try {
            $pdo->prepare(
                'INSERT INTO reviews (product_id,user_id,order_id,rating,content,status) VALUES (:p,:u,:o,:r,:c,\'pending\')'
            )->execute(['p' => $productId, 'u' => Auth::id(), 'o' => $orderId ?: null, 'r' => $rating, 'c' => $r->input('content')]);
            $rid = (int) $pdo->lastInsertId();
            if (!empty($r->files['images']['name'][0])) {
                $files = $this->restructure($r->files['images']);
                foreach (array_slice($files, 0, 3) as $file) {
                    $path = Uploader::image($file, 'reviews');
                    if ($path) {
                        $pdo->prepare('INSERT INTO review_images (review_id, path) VALUES (:r,:p)')->execute(['r' => $rid, 'p' => $path]);
                    }
                }
            }
            Session::flash('success', 'Đánh giá đã gửi, chờ duyệt.');
        } catch (\PDOException) {
            Session::flash('error', 'Bạn đã đánh giá sản phẩm này.');
        }
        redirect(url('/san-pham/' . $product['slug']));
    }

    public function update(Request $r, array $p): void
    {
        $review = Review::owned((int) $r->input('id'), Auth::id());
        if (!$review) {
            Response::abort(403);
        }
        $rating = (int) $r->input('rating');
        $content = trim((string) $r->input('content'));
        if ($rating < 1 || $rating > 5 || mb_strlen($content) < 5) {
            Session::flash('error', 'Dữ liệu không hợp lệ.');
            back();
        }
        Database::connection()->prepare(
            "UPDATE reviews SET rating=:r, content=:c, status='pending' WHERE id=:id AND user_id=:u"
        )->execute(['r' => $rating, 'c' => $content, 'id' => $review['id'], 'u' => Auth::id()]);
        Product::recountRating((int) $review['product_id']);
        Session::flash('success', 'Đã cập nhật đánh giá (chờ duyệt lại).');
        back();
    }

    public function delete(Request $r, array $p): void
    {
        $review = Review::owned((int) $r->input('id'), Auth::id());
        if (!$review) {
            Response::abort(403);
        }
        $pid = (int) $review['product_id'];
        Database::connection()->prepare('DELETE FROM reviews WHERE id=:id AND user_id=:u')->execute(['id' => $review['id'], 'u' => Auth::id()]);
        Product::recountRating($pid);
        Session::flash('success', 'Đã xóa đánh giá.');
        back();
    }

    private function restructure(array $file): array
    {
        $out = [];
        foreach ($file['name'] as $i => $name) {
            $out[] = [
                'name' => $name,
                'type' => $file['type'][$i],
                'tmp_name' => $file['tmp_name'][$i],
                'error' => $file['error'][$i],
                'size' => $file['size'][$i],
            ];
        }
        return $out;
    }
}
