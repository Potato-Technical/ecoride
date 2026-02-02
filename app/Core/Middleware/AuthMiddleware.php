<?php

namespace App\Core\Middleware;

class AuthMiddleware
{
    public static function handle(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }
}