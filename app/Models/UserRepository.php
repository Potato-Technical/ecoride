<?php

namespace App\Models; // Namespace des repositories

use App\Core\Database; // Accès PDO
use PDO;               // FETCH_ASSOC

class UserRepository
{
    /**
     * Recherche un utilisateur par email
     * Utilisé pour l'authentification
     */
    public function findByEmail(string $email): ?array
    {
        // Connexion PDO
        $pdo = Database::getInstance();

        // Préparation de la requête SQL
        $stmt = $pdo->prepare(
            'SELECT * FROM utilisateur WHERE email = :email'
        );

        // Exécution sécurisée
        $stmt->execute([
            'email' => $email
        ]);

        // Récupération du résultat
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retourne null si aucun utilisateur
        return $user ?: null;
    }

    /**
     * Recherche un utilisateur par identifiant
     */
    public function findById(int $id): ?array
    {
        // Connexion PDO
        $pdo = Database::getInstance();

        // Préparation de la requête SQL
        $stmt = $pdo->prepare(
            'SELECT * FROM utilisateur WHERE id = :id'
        );

        // Exécution sécurisée
        $stmt->execute([
            'id' => $id
        ]);

        // Récupération du résultat
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retourne null si absent
        return $user ?: null;
    }
}
