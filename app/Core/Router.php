<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<int, array{method:string,pattern:string,handler:mixed,middleware:array}> */
    private array $routes = [];

    public function get(string $pattern, mixed $handler, array $middleware = []): void
    {
        $this->add('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, mixed $handler, array $middleware = []): void
    {
        $this->add('POST', $pattern, $handler, $middleware);
    }

    public function add(string $method, string $pattern, mixed $handler, array $middleware = []): void
    {
        $this->routes[] = compact('method', 'pattern', 'handler', 'middleware');
    }

    public function dispatch(Request $request): void
    {
        $path = rtrim($request->path, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method && !($route['method'] === 'GET' && $request->method === 'HEAD')) {
                continue;
            }
            $regex = $this->toRegex($route['pattern']);
            if (!preg_match($regex, $path, $matches)) {
                continue;
            }
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            foreach ($route['middleware'] as $mw) {
                $class = is_string($mw) ? $mw : $mw[0];
                $instance = new $class();
                $instance->handle($request, $params);
            }

            if (is_array($route['handler'])) {
                [$class, $method] = $route['handler'];
                $controller = new $class();
                $controller->{$method}($request, $params);
                return;
            }

            ($route['handler'])($request, $params);
            return;
        }

        if ($request->wantsJson()) {
            Response::json(['error' => 'Not found'], 404);
        }
        Response::abort(404);
    }

    private function toRegex(string $pattern): string
    {
        $pattern = rtrim($pattern, '/') ?: '/';
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $regex . '$#';
    }
}
