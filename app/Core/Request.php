<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query,
        public readonly array $post,
        public readonly array $files,
        public readonly array $server,
        public readonly array $headers,
        public readonly string $body,
    ) {
    }

    public static function capture(): self
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
        if ($base !== '' && $base !== '/' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base)) ?: '/';
        }

        $headers = [];
        foreach ($_SERVER as $k => $v) {
            if (str_starts_with($k, 'HTTP_')) {
                $headers[str_replace('_', '-', strtolower(substr($k, 5)))] = $v;
            }
        }

        return new self(
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            '/' . ltrim($path, '/'),
            $_GET,
            $_POST,
            $_FILES,
            $_SERVER,
            $headers,
            (string) file_get_contents('php://input'),
        );
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    public function ip(): string
    {
        return (string) ($this->server['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    public function userAgent(): string
    {
        return substr((string) ($this->server['HTTP_USER_AGENT'] ?? ''), 0, 255);
    }

    public function bearerToken(): ?string
    {
        $auth = $this->headers['authorization'] ?? $this->server['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(\S+)/i', (string) $auth, $m)) {
            return $m[1];
        }
        return null;
    }

    public function wantsJson(): bool
    {
        $accept = $this->headers['accept'] ?? '';
        return str_contains((string) $accept, 'application/json') || str_starts_with($this->path, '/api/');
    }
}
