<?php
namespace App\Config;

use PDO;
use PDOException;

/**
 * Classe Database
 * Fournit une connexion PDO centralisée (pattern Singleton)
 * Utilisée par les Models via Database::pdo()
 */
class Database
{
    // Instance unique de PDO (null tant qu’elle n’est pas créée)
    private static ?PDO $pdo = null;

    /**
     * Retourne une instance PDO unique
     * @return PDO
     */
    public static function pdo(): PDO
    {
        // Si la connexion n’existe pas encore, je la crée
        if (self::$pdo === null) {
            try {
                // Paramètres de connexion ( à sécuriser en prod)
                $host = 'localhost';
                $dbname = 'ecoride';
                $username = 'ekko';
                $password = '1234';

                // DSN PDO
                $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

                // Options PDO
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // exceptions en cas d’erreur
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // fetch en tableaux associatifs
                    PDO::ATTR_EMULATE_PREPARES   => false,                 // sécurité : pas d’émulation
                ];

                // Création de l’instance PDO
                self::$pdo = new PDO($dsn, $username, $password, $options);

            } catch (PDOException $e) {
                error_log("[".date('Y-m-d H:i:s')."] Connexion DB : " . $e->getMessage() . "\n", 3, __DIR__."/../logs/error.log");
                // Erreur de connexion → stop exécution
                die('Erreur de connexion à la base de données.');
            }
        }

        // Retourne toujours la même instance
        return self::$pdo;
    }
}
