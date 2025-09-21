<?php
namespace App\Core;

/**
 * Classe utilitaire de sécurité
 * - Protection XSS (échappement HTML)
 * - Protection CSRF (génération et vérification de token)
 */
final class Security
{
    /**
     * Échappe les caractères HTML pour éviter les failles XSS
     *
     * @param string $value
     * @return string
     */
    public static function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Génère un champ hidden avec un token CSRF
     * À insérer dans chaque formulaire POST
     *
     * @return string
     */
    public static function csrfField(): string
    {
        if (!isset($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return '<input type="hidden" name="_csrf" value="' . $_SESSION['_csrf'] . '">';
    }

    /**
     * Vérifie qu’un token CSRF reçu correspond à celui en session
     *
     * @param string|null $token
     * @return bool
     */
    public static function verifyCsrf(?string $token): bool
    {
        return isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token ?? '');
    }
}
