<?php

// Configuration de la connexion à la base de données
return [
    'host' => $_ENV['DB_HOST'],
    'dbname' => $_ENV['DB_NAME'],
    'user' => $_ENV['DB_USER'],
    'pass' => $_ENV['DB_PASS'],
    'port' => $_ENV['DB_PORT'],
];
