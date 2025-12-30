<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null; // Contiendra l’unique instance PDO

    public static function getInstance(): PDO // Méthode appelée partout dans le projet
    {
        // Si la connexion existe déjà, on la réutilise
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        // Chargement de la configuration
        $config = require dirname(__DIR__, 2) . '/config/database.php';

        try {
            self::$pdo = new PDO( // Création de la connexion PDO
                "mysql:host={$config['host']};dbname={$config['dbname']};port={$config['port']};charset=utf8mb4",
                $config['user'],
                $config['pass'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Les erreurs PDO lèvent des exceptions
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Les résultats sont retournés en tableaux associatifs
                ]
            );
        } catch (PDOException $e) {
            // Erreur critique : l'application ne peut pas fonctionner
            die('Erreur de connexion à la base de données');
        }

        return self::$pdo;
    }
}
