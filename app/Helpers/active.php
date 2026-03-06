<?php

function active(string $route): string
{
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if ($route === '/') {
        return $path === '/' ? 'active' : '';
    }

    return str_starts_with($path, $route) ? 'active' : '';
}