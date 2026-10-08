<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public static function ensureToken(): void
    {
        if (!Session::get('_csrf')) {
            Session::set('_csrf', bin2hex(random_bytes(32)));
        }
    }

    public static function token(): string
    {
        self::ensureToken();
        return (string) Session::get('_csrf');
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    public static function verify(?string $token): bool
    {
        $session = (string) Session::get('_csrf', '');
        if ($session === '' || $token === null || $token === '') {
            return false;
        }
        return hash_equals($session, $token);
    }
}
