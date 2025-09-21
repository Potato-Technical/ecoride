<?php
// config/config.sample.php
// Copier ce fichier comme config/config.php et renseigner les valeurs locales.
// NE PAS COMMITTER config/config.php (doit être présent dans .gitignore).

return [
    // Base de données
    'db' => [
        'host'     => 'localhost',
        'port'     => 3306,
        'dbname'   => 'ecoride',
        'user'     => 'db_user',
        'password' => 'db_password',
        'charset'  => 'utf8mb4'
    ],

    // Dev / prod mode
    'app' => [
        'env'  => 'development',   // production | development
        'debug'=> true
    ],

    // Paramètres généraux (ex : URL base, email support)
    'app_settings' => [
        'base_url'      => 'http://ecoride.local', // adapter en prod
        'support_email' => 'support@ecoride.example'
    ],

    // Options PDO (sécurisées)
    'pdo_options' => [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES  => false
    ]
];
