#!/usr/bin/env php
<?php

$envPath = __DIR__ . '/../.env';

if (is_readable($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (!str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
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
    $pdo = Database::getInstance();

    $ping = (int)$pdo->query('SELECT 1')->fetchColumn();
    $count = (int)$pdo->query('SELECT COUNT(*) FROM utilisateur')->fetchColumn();

    echo "[OK] env: DB_HOST=" . getenv('DB_HOST') .
         " DB_NAME=" . getenv('DB_NAME') . PHP_EOL;

    echo "[OK] ping: $ping" . PHP_EOL;
    echo "[OK] utilisateur count: $count" . PHP_EOL;

    if ($count < 1) {
        fwrite(STDERR, "[FAIL] no users\n");
        exit(1);
    }

    exit(0);

} catch (Throwable $e) {
    echo "[FAIL] " . $e->getMessage() . PHP_EOL;
    exit(1);
}