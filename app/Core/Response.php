<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    public static function redirect(string $to, int $code = 302): never
    {
        header('Location: ' . $to, true, $code);
        exit;
    }

    public static function json(mixed $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function abort(int $code, string $message = ''): never
    {
        http_response_code($code);
        $view = match ($code) {
            403 => 'errors/403',
            404 => 'errors/404',
            default => 'errors/500',
        };
        View::render($view, ['message' => $message, 'title' => (string) $code], 'layouts/shop');
        exit;
    }
}
