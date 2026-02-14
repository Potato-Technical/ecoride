<?php

namespace App\Core\Middleware;

use App\Controllers\ErrorController;

class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(callable $next): void
    {
        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!function_exists('csrf_is_valid')) {
            require_once dirname(__DIR__, 2) . '/Helpers/csrf.php';
        }

        if (!csrf_is_valid(is_string($token) ? $token : null)) {
            (new ErrorController())->forbidden();
            return;
        }

        $next();
    }
}
