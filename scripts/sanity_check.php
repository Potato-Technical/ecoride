#!/usr/bin/env php
<?php

$envPath = __DIR__ . '/../.env';
if (is_readable($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (!str_contains($line, '=')) continue;

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }

        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
} else {
    echo "[WARN] .env introuvable: $envPath\n";
}

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();

    $ok = $db->query('SELECT 1')->fetchColumn();
    $count = $db->query('SELECT COUNT(*) FROM utilisateur')->fetchColumn();

    echo "[OK] env: DB_HOST=" . (getenv('DB_HOST') ?: 'null') . " DB_NAME=" . (getenv('DB_NAME') ?: 'null') . "\n";
    echo "[OK] ping: $ok\n";
    echo "[OK] utilisateur count: $count\n";
    exit(0);

} catch (Throwable $e) {
    echo "[FAIL] " . $e->getMessage() . "\n";
    exit(1);
}