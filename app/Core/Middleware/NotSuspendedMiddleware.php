<?php

namespace App\Core\Middleware;

use App\Controllers\ErrorController;
use App\Core\Database;

class NotSuspendedMiddleware implements MiddlewareInterface
{
    public function handle(callable $next): void
    {
        if (empty($_SESSION['user_id'])) {
            $next();
            return;
        }

        $pdo = Database::getInstance();

        $stmt = $pdo->prepare('SELECT est_suspendu FROM utilisateur WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => (int)$_SESSION['user_id']]);

        $estSuspendu = (bool) $stmt->fetchColumn();

        if ($estSuspendu) {
            (new ErrorController())->forbidden();
            return;
        }

        $next();
    }
}