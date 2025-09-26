<?php
namespace App\Config;

use PDO;
use PDOException;

/**
 * Classe Database
 * Gère une connexion PDO unique (pattern Singleton).
 * Tous les modèles et contrôleurs passent par Database::get().
 */
class Database
{
    /**
     * Instance unique de PDO (créée au premier appel).
     * @var PDO|null
     */
    private static ?PDO $pdo = null;

    /**
     * Retourne l'instance PDO (la crée si nécessaire).
     * @return PDO
     */
    public static function get(): PDO
    {
        if (self::$pdo === null) {
            // Paramètres de connexion : à adapter selon ton serveur
            $host     = 'localhost';     // ou 'localhost'
            $dbname   = 'ecoride';       // nom de ta base
            $username = 'root';          // utilisateur MySQL (ou autre si config différente)
            $password = '';          // mot de passe MySQL

            // DSN PDO
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

            // Options PDO
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // lever exception en cas d'erreur
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // fetch en tableau associatif
                PDO::ATTR_EMULATE_PREPARES   => false,                 // sécurité : désactive l'émulation
            ];

            try {
                // Création de la connexion PDO
                self::$pdo = new PDO($dsn, $username, $password, $options);

            } catch (PDOException $e) {
                // Log de l'erreur (utile en prod)
                error_log(
                    "[" . date('Y-m-d H:i:s') . "] Connexion DB : " . $e->getMessage() . "\n",
                    3,
                    __DIR__ . "/../logs/error.log"
                );
                // Erreur critique → arrêt exécution
                die('Erreur de connexion à la base de données.');
            }
        }
        // Retourne toujours la même instance
        return self::$pdo;
    }
}
