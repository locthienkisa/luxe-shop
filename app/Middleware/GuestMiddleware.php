<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

final class GuestMiddleware
{
    public function handle(Request $request, array $params): void
    {
        if (Auth::check()) {
            Response::redirect(url('/tai-khoan'));
        }
    }
}
