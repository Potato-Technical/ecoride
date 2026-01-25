#!/usr/bin/env php
<?php

if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        putenv($line);
    }
}

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;


$db = Database::getInstance();
$stmt = $db->query('SELECT COUNT(*) FROM utilisateur');

echo "Nombre total d’utilisateurs: " . $stmt->fetchColumn() . PHP_EOL;
