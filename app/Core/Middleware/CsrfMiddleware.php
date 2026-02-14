<?php

namespace App\Core\Middleware;

use App\Controllers\ErrorController;

class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(callable $next): void
    {
        $token = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

        if (!csrf_is_valid(is_string($token) ? $token : null)) {
            (new ErrorController())->forbidden();
            return;
        }

        $next();
    }
}
