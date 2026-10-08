<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\ApiToken;

final class ApiAuthMiddleware
{
    public function handle(Request $request, array $params): void
    {
        $token = $request->bearerToken();
        if (!$token) {
            Response::json(['error' => 'Missing token'], 401);
        }
        $user = ApiToken::userByPlain($token);
        if (!$user) {
            Response::json(['error' => 'Invalid token'], 401);
        }
        Session::set('user_id', (int) $user['id']);
        Auth::boot();
    }
}
