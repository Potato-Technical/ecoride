<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    // Contiendra l’unique instance PDO
    private static ?PDO $pdo = null;

    // Méthode appelée partout dans le projet
    public static function getInstance(): PDO
    {
        // Si la connexion existe déjà, on la réutilise
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        // On charge la config
        $config = require dirname(__DIR__, 2) . '/config/database.php';

        try {
            // Création de la connexion PDO
            self::$pdo = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']};port={$config['port']};charset=utf8mb4",
                $config['user'],
                $config['pass'],
                [
                    // Les erreurs PDO lèvent des exceptions
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                    // Les résultats sont retournés en tableaux associatifs
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            // En prod on log, ici on bloque net
            die('Erreur de connexion à la base de données');
        }

        return self::$pdo;
    }
}
