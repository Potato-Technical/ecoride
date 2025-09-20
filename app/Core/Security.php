<?php
namespace App\Core;

final class Security
{
    /** Echappement HTML (XSS) */
    public static function h(null|string $v): string {
        return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** Chaîne nettoyée (trim, espaces normalisés) */
    public static function sanitizeString(?string $v, int $maxLen = 255): string {
        $v = trim((string)$v);
        $v = preg_replace('/\s+/u', ' ', $v ?? '');
        return mb_substr($v, 0, $maxLen, 'UTF-8');
    }

    /** Entier borné */
    public static function int(mixed $v, ?int $min=null, ?int $max=null): ?int {
        if (!is_numeric($v)) return null;
        $i = (int)$v;
        if (($min !== null && $i < $min) || ($max !== null && $i > $max)) return null;
        return $i;
    }

    /** Flottant borné */
    public static function float(mixed $v, ?float $min=null, ?float $max=null): ?float {
        if (!is_numeric($v)) return null;
        $f = (float)$v;
        if (($min !== null && $f < $min) || ($max !== null && $f > $max)) return null;
        return $f;
    }

    /** Date (YYYY-MM-DD) valide -> string normalisée */
    public static function date(?string $v): ?string {
        if (!$v) return null;
        $dt = \DateTime::createFromFormat('Y-m-d', $v);
        return $dt && $dt->format('Y-m-d') === $v ? $v : null;
    }

    /** Heure (HH:MM) valide -> string normalisée */
    public static function time(?string $v): ?string {
        if (!$v) return null;
        $dt = \DateTime::createFromFormat('H:i', $v);
        return $dt && $dt->format('H:i') === $v ? $v : null;
    }

    /** CSRF token (session) */
    public static function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /** Champ hidden CSRF à insérer dans les formulaires */
    public static function csrfField(): string {
        $token = self::csrfToken();
        return '<input type="hidden" name="_csrf" value="'.self::h($token).'">';
    }

    /** Vérifie et invalide le token CSRF consommé */
    public static function verifyCsrf(?string $token): bool {
        if (!$token || empty($_SESSION['csrf_token'])) return false;
        $ok = hash_equals($_SESSION['csrf_token'], $token);
        if ($ok) {
            // Protection re-submit : régénérer le token
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $ok;
    }

    /**
     * Filtrage déclaratif des inputs
     * Exemple schéma :
     * [
     *  'depart'  => fn($v)=> self::sanitizeString($v, 120),
     *  'places'  => fn($v)=> self::int($v, 1, 8),
     * ]
     */
    public static function filterArray(array $input, array $schema): array {
        $out = [];
        foreach ($schema as $key => $fn) {
            $val = $input[$key] ?? null;
            $out[$key] = is_callable($fn) ? $fn($val) : null;
        }
        return $out;
    }
}
