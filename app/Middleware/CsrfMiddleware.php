<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

final class CsrfMiddleware
{
    public function handle(Request $request, array $params): void
    {
        if (!in_array($request->method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return;
        }
        $token = (string) $request->input('_csrf', $request->headers['x-csrf-token'] ?? '');
        if (!Csrf::verify($token)) {
            if ($request->wantsJson()) {
                Response::json(['error' => 'CSRF token không hợp lệ'], 419);
            }
            Session::flash('error', 'Phiên làm việc hết hạn. Vui lòng thử lại.');
            Response::redirect(url('/'));
        }
    }
}
