<?php
namespace App\Core;

/**
 * Classe utilitaire de sécurité
 * - Protection XSS (échappement HTML)
 * - Protection CSRF (génération et vérification de token)
 * - Vérification des rôles utilisateurs
 */
final class Security
{
    public static function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public static function csrfField(): string
    {
        if (!isset($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return '<input type="hidden" name="_csrf" value="' . $_SESSION['_csrf'] . '">';
    }

    public static function verifyCsrf(?string $token): bool
    {
        return isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token ?? '');
    }

    /**
     * Vérifie que l’utilisateur connecté possède un des rôles requis.
     * Sinon redirige vers la page 403.
     */
    public static function requireRole(array $roles): void
    {
        if (empty($_SESSION['user']) || !in_array($_SESSION['user']['role'], $roles, true)) {
            header('Location: /403');
            exit;
        }
    }
}
