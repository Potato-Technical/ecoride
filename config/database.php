<?php
// Active l'affichage des erreurs (à désactiver en production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Fonction de connexion PDO centralisée
function getPDO() {
    try {
        $host = 'localhost';
        $dbname = 'ecoride';
        $username = 'ekko';
        $password = '1234'; // à adapter à ton environnement

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // erreurs en exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // fetch en tableau associatif
            PDO::ATTR_EMULATE_PREPARES => false // sécurité : pas d’émulation
        ];

        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}
