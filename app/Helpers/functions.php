<?php

declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function env_val(string $key, mixed $default = null): mixed
{
    return \App\Core\Config::env($key, $default);
}

function config(string $key, mixed $default = null): mixed
{
    return \App\Core\Config::get($key, $default);
}

function url(string $path = '/'): string
{
    $base = rtrim((string) config('app.url', ''), '/');
    $path = '/' . ltrim($path, '/');
    if ($path === '/') {
        return $base . '/';
    }
    return $base . $path;
}

function asset(string $path): string
{
    return url('/' . ltrim($path, '/'));
}

function csrf_field(): string
{
    return \App\Core\Csrf::field();
}

function csrf_token(): string
{
    return \App\Core\Csrf::token();
}

function old(string $key, mixed $default = ''): mixed
{
    $old = \App\Core\Session::get('_old', []);
    return $old[$key] ?? $default;
}

function redirect(string $to): never
{
    \App\Core\Response::redirect($to);
}

function back(): never
{
    $ref = (string) ($_SERVER['HTTP_REFERER'] ?? '');
    $base = rtrim((string) config('app.url', ''), '/');
    if ($ref !== '' && $base !== '' && str_starts_with($ref, $base . '/')) {
        \App\Core\Response::redirect($ref);
    }
    if ($ref !== '' && $base !== '' && rtrim($ref, '/') === $base) {
        \App\Core\Response::redirect($ref);
    }
    \App\Core\Response::redirect(url('/'));
}

function safe_href(?string $url): string
{
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }
    if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
        return $url;
    }
    $parts = parse_url($url);
    $scheme = strtolower((string) ($parts['scheme'] ?? ''));
    if (in_array($scheme, ['http', 'https'], true)) {
        return $url;
    }
    return '';
}

function clamp_offset(int $total, int $offset, int $limit): int
{
    $limit = max(1, $limit);
    $offset = max(0, $offset);
    if ($total <= 0) {
        return 0;
    }
    if ($offset < $total) {
        return $offset;
    }
    $pages = max(1, (int) ceil($total / $limit));
    return ($pages - 1) * $limit;
}

function flash(string $key, mixed $value = null): mixed
{
    return \App\Core\Session::flash($key, $value);
}

function money(float|int|string $amount): string
{
    return number_format((float) $amount, 0, ',', '.') . '₫';
}

function product_price(array $product): float
{
    $sale = $product['sale_price'] ?? null;
    if ($sale !== null && $sale !== '' && (float) $sale > 0 && (float) $sale < (float) $product['price']) {
        return (float) $sale;
    }
    return (float) $product['price'];
}

function slugify(string $text): string
{
    $map = [
        'à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a','â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a','ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a',
        'è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e','ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e',
        'ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i',
        'ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o','ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o','ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o',
        'ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u','ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u',
        'ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y','đ'=>'d',
        'À'=>'a','Á'=>'a','Ạ'=>'a','Ả'=>'a','Ã'=>'a','Â'=>'a','Ầ'=>'a','Ấ'=>'a','Ậ'=>'a','Ẩ'=>'a','Ẫ'=>'a','Ă'=>'a','Ằ'=>'a','Ắ'=>'a','Ặ'=>'a','Ẳ'=>'a','Ẵ'=>'a',
        'È'=>'e','É'=>'e','Ẹ'=>'e','Ẻ'=>'e','Ẽ'=>'e','Ê'=>'e','Ề'=>'e','Ế'=>'e','Ệ'=>'e','Ể'=>'e','Ễ'=>'e',
        'Ì'=>'i','Í'=>'i','Ị'=>'i','Ỉ'=>'i','Ĩ'=>'i',
        'Ò'=>'o','Ó'=>'o','Ọ'=>'o','Ỏ'=>'o','Õ'=>'o','Ô'=>'o','Ồ'=>'o','Ố'=>'o','Ộ'=>'o','Ổ'=>'o','Ỗ'=>'o','Ơ'=>'o','Ờ'=>'o','Ớ'=>'o','Ợ'=>'o','Ở'=>'o','Ỡ'=>'o',
        'Ù'=>'u','Ú'=>'u','Ụ'=>'u','Ủ'=>'u','Ũ'=>'u','Ư'=>'u','Ừ'=>'u','Ứ'=>'u','Ự'=>'u','Ử'=>'u','Ữ'=>'u',
        'Ỳ'=>'y','Ý'=>'y','Ỵ'=>'y','Ỷ'=>'y','Ỹ'=>'y','Đ'=>'d',
    ];
    $text = strtr($text, $map);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-') ?: 'item';
}

function order_status_label(string $status): string
{
    return match ($status) {
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'processing' => 'Đang xử lý',
        'shipping' => 'Đang giao',
        'delivered' => 'Đã giao',
        'cancelled' => 'Đã hủy',
        'refunded' => 'Hoàn tiền',
        default => $status,
    };
}

function setting(string $key, mixed $default = null): mixed
{
    return \App\Models\Setting::get($key, $default);
}

function paginate_meta(int $total, int $page, int $perPage): array
{
    $pages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($page, $pages));
    return [
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'pages' => $pages,
        'offset' => ($page - 1) * $perPage,
    ];
}
