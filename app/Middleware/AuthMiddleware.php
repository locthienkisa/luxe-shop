<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

final class AuthMiddleware
{
    public function handle(Request $request, array $params): void
    {
        if (!Auth::check()) {
            if ($request->wantsJson()) {
                Response::json(['error' => 'Unauthenticated'], 401);
            }
            Session::set('intended', $request->path);
            Response::redirect(url('/dang-nhap'));
        }
        if ((int) (Auth::user()['must_change_password'] ?? 0) === 1 && $request->path !== '/tai-khoan/doi-mat-khau') {
            Session::flash('error', 'Bạn phải đổi mật khẩu mặc định trước khi tiếp tục.');
            Response::redirect(url('/tai-khoan/doi-mat-khau'));
        }
    }
}
