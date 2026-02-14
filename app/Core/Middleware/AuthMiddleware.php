<?php

// App/Core/Middleware/AuthMiddleware.php
namespace App\Core\Middleware;

use App\Controllers\ErrorController;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(callable $next): void
    {
        if (!empty($_SESSION['user_id'])) {
            $next();
            return;
        }

        $redirect = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        $blocked = ['/logout', '/login', '/register'];
        if (in_array($redirect, $blocked, true)) {
            $redirect = '/';
        }

        // si /logout ou autre => fallback
        if (in_array($redirect, $blocked, true)) {
            $redirect = '/';
        }

        header('Location: /login?redirect=' . urlencode($redirect));
        exit;
    }
}