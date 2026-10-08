<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    private static array $shared = [];

    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    public static function render(string $template, array $data = [], ?string $layout = 'layouts/shop'): void
    {
        $data = array_merge(self::$shared, $data);
        extract($data, EXTR_SKIP);
        $viewFile = dirname(__DIR__) . '/Views/' . $template . '.php';
        if (!is_file($viewFile)) {
            throw new \RuntimeException('View not found');
        }
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }
        $layoutFile = dirname(__DIR__) . '/Views/' . $layout . '.php';
        include $layoutFile;
    }

    public static function renderToString(string $template, array $data = []): string
    {
        ob_start();
        self::render($template, $data, null);
        return (string) ob_get_clean();
    }
}
