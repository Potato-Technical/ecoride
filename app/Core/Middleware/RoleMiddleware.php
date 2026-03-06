<?php

namespace App\Core\Middleware;

class RoleMiddleware implements MiddlewareInterface
{
    private string $role;

    public function __construct(string $role = '')
    {
        $this->role = $role;
    }

    public function handle(callable $next): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $currentRole = $_SESSION['role'] ?? null;

        if ($this->role && $currentRole !== $this->role) {
            http_response_code(403);
            require __DIR__ . '/../../Views/errors/403.php';
            exit;
        }

        $next();
    }
}