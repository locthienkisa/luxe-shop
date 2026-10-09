<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Models\ApiToken;
use App\Models\LoginAttempt;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use App\Services\CheckoutService;

final class ApiController
{
    public function products(Request $r, array $p): void
    {
        $page = max(1, (int) ($r->query['page'] ?? 1));
        $res = Product::search([
            'q' => $r->query['q'] ?? '',
            'brand' => $r->query['brand'] ?? '',
            'category' => $r->query['category'] ?? '',
            'sort' => $r->query['sort'] ?? 'newest',
        ], ($page - 1) * 12, 12);
        Response::json(['data' => $res['rows'], 'total' => $res['total'], 'page' => $page]);
    }

    public function product(Request $r, array $p): void
    {
        $product = Product::findBySlug((string) $p['slug']);
        if (!$product || !(int) $product['is_active']) {
            Response::json(['error' => 'Not found'], 404);
        }
        $product['images'] = Product::images((int) $product['id']);
        $product['variants'] = Product::variants((int) $product['id']);
        Response::json(['data' => $product]);
    }

    public function login(Request $r, array $p): void
    {
        $data = json_decode($r->body, true) ?: $r->post;
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['password'] ?? '');
        $maxAttempts = (int) config('app.login_max_attempts', 5);
        $lockoutMinutes = (int) config('app.login_lockout_minutes', 15);
        if (LoginAttempt::tooMany($email, $r->ip(), $maxAttempts, $lockoutMinutes)) {
            Response::json(['error' => 'Too many login attempts'], 429);
        }
        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash']) || !(int) $user['is_active']) {
            LoginAttempt::record($email, $r->ip(), false);
            Response::json(['error' => 'Invalid credentials'], 401);
        }
        LoginAttempt::record($email, $r->ip(), true);
        $token = ApiToken::issue((int) $user['id']);
        Response::json(['token' => $token, 'user' => ['id' => $user['id'], 'email' => $user['email'], 'name' => $user['full_name']]]);
    }

    public function register(Request $r, array $p): void
    {
        $data = json_decode($r->body, true) ?: $r->post;
        $errors = Validator::validate($data, ['email' => 'required|email', 'password' => 'required|min:8', 'full_name' => 'required']);
        if ($errors) {
            Response::json(['errors' => $errors], 422);
        }
        if (User::findByEmail(strtolower($data['email']))) {
            Response::json(['error' => 'Email exists'], 409);
        }
        $id = User::create([
            'role_id' => User::roleIdBySlug('customer'),
            'email' => strtolower($data['email']),
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'full_name' => $data['full_name'],
        ]);
        $token = ApiToken::issue($id);
        Response::json(['token' => $token, 'user_id' => $id], 201);
    }

    public function cart(Request $r, array $p): void
    {
        $data = json_decode($r->body, true) ?: $r->post;
        try {
            CartService::add((int) ($data['product_id'] ?? 0), !empty($data['variant_id']) ? (int) $data['variant_id'] : null, max(1, (int) ($data['quantity'] ?? 1)));
            Response::json(CartService::totals());
        } catch (\Throwable $e) {
            Response::json(['error' => $e->getMessage()], 422);
        }
    }

    public function checkout(Request $r, array $p): void
    {
        $data = json_decode($r->body, true) ?: $r->post;
        try {
            $order = CheckoutService::place($data);
            Response::json($order, 201);
        } catch (\Throwable $e) {
            Response::json(['error' => $e->getMessage()], 422);
        }
    }

    public function orders(Request $r, array $p): void
    {
        $res = Order::forUser(Auth::id(), 0, 50);
        Response::json(['data' => $res['rows']]);
    }

    public function profile(Request $r, array $p): void
    {
        $u = Auth::user();
        unset($u['password_hash']);
        Response::json(['data' => $u]);
    }
}
