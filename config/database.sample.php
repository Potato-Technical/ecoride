<?php

// Configuration de la connexion à la base de données
return [
  'host'   => $_ENV['DB_HOST'] ?? 'localhost',
  'dbname' => $_ENV['DB_NAME'] ?? 'ecoride',
  'user'   => $_ENV['DB_USER'] ?? 'user',
  'pass'   => $_ENV['DB_PASS'] ?? 'secret',
  'port'   => $_ENV['DB_PORT'] ?? 3306,
];
