<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

final class AdminMiddleware
{
    public function handle(Request $request, array $params): void
    {
        if (!Auth::check()) {
            Response::redirect(url('/dang-nhap'));
        }
        if (!Auth::isStaff()) {
            Response::abort(403, 'Bạn không có quyền truy cập khu vực quản trị.');
        }
        $path = rtrim($request->path, '/') ?: '/';
        if ((int) (Auth::user()['must_change_password'] ?? 0) === 1 && $path !== '/tai-khoan/doi-mat-khau') {
            Session::flash('error', 'Bạn phải đổi mật khẩu mặc định trước khi tiếp tục.');
            Response::redirect(url('/tai-khoan/doi-mat-khau'));
        }
    }
}
