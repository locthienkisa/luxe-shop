<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

final class SuperAdminMiddleware
{
    public function handle(Request $request, array $params): void
    {
        if (!Auth::hasRole('super_admin')) {
            Response::abort(403, 'Chỉ siêu quản trị viên mới thực hiện được thao tác này.');
        }
    }
}
