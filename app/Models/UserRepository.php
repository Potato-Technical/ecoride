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
     * Récupère un utilisateur à partir de son pseudo.
     */
    public function findByPseudo(string $pseudo): ?array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            'SELECT * FROM utilisateur WHERE pseudo = :pseudo'
        );

        $stmt->execute([
            'pseudo' => $pseudo
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

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

    /**
     * Crée un nouvel utilisateur.
     */
    public function create(array $data): void
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            'INSERT INTO utilisateur (
                pseudo,
                email,
                mot_de_passe_hash,
                role_id
            ) VALUES (
                :pseudo,
                :email,
                :mot_de_passe_hash,
                :role_id
            )'
        );

        $stmt->execute([
            'pseudo'            => $data['pseudo'],
            'email'             => $data['email'],
            'mot_de_passe_hash' => $data['mot_de_passe_hash'],
            'role_id'           => $data['role_id']
        ]);
    }
}
