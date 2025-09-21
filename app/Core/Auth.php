<?php
// app/Core/Auth.php
class Auth
{
    public static function check()
    {
        return !empty($_SESSION['user']['id']);
    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function isRole($role)
    {
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $role;
    }

    public static function requireRole($role)
    {
        if (!self::check() || !self::isRole($role)) {
            header('Location: /login');
            exit;
        }
    }
}
