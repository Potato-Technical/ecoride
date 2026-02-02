<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class VehiculeRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /**
     * Retourne le premier véhicule de l'utilisateur (id) ou null s'il n'en a pas.
     *
     * Usage :
     * - Résolution automatique du véhicule lors de la création d’un trajet
     *   (évite un vehicule_id codé en dur).
     *
     * @param int $userId Identifiant de l’utilisateur (chauffeur)
     * @return array|null Tableau contenant au minimum ['id'] ou null si aucun véhicule
     */
    public function findFirstByUserId(int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id
            FROM vehicule
            WHERE utilisateur_id = :uid
            ORDER BY id ASC
            LIMIT 1
        ");
        $stmt->execute(['uid' => $userId]);

        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Crée un véhicule associé à un utilisateur.
     *
     * Règles implicites :
     * - Le contrôleur valide les champs (format immatriculation, date, etc.)
     * - Le propriétaire est déterminé par utilisateur_id (clé étrangère).
     *
     * @param array $data Données véhicule (immatriculation, date, marque, etc.)
     * @return void
     */
    public function create(array $data): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO vehicule (
                immatriculation,
                date_premiere_immatriculation,
                modele,
                marque,
                couleur,
                energie,
                fumeur_accepte,
                animaux_acceptes,
                utilisateur_id
            ) VALUES (
                :immatriculation,
                :date_premiere_immatriculation,
                :modele,
                :marque,
                :couleur,
                :energie,
                :fumeur_accepte,
                :animaux_acceptes,
                :utilisateur_id
            )
        ");

        $stmt->execute([
            'immatriculation'               => $data['immatriculation'],
            'date_premiere_immatriculation' => $data['date_premiere_immatriculation'],
            'modele'                        => $data['modele'],
            'marque'                        => $data['marque'],
            'couleur'                       => $data['couleur'],
            'energie'                       => $data['energie'],
            'fumeur_accepte'                => (int)$data['fumeur_accepte'],
            'animaux_acceptes'              => (int)$data['animaux_acceptes'],
            'utilisateur_id'                => (int)$data['utilisateur_id'],
        ]);
    }

    /**
     * Retourne la liste des véhicules d’un utilisateur.
     *
     * Usage :
     * - Affichage “Mes véhicules”
     * - Sélection d’un véhicule lors de la création de trajet (si UI prévue)
     *
     * @param int $userId Identifiant de l’utilisateur
     * @return array Liste des véhicules (peut être vide)
     */
    public function findAllByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, immatriculation, marque, modele, energie
            FROM vehicule
            WHERE utilisateur_id = :uid
            ORDER BY id ASC
        ");
        $stmt->execute(['uid' => $userId]);

        return $stmt->fetchAll() ?: [];
    }

    /**
     * Vérifie qu’un véhicule appartient à un utilisateur.
     *
     * Usage :
     * - Contrôle d’ownership avant modification/suppression
     * - Protection contre l’accès à un véhicule d’un autre compte
     *
     * @param int $vehiculeId Identifiant du véhicule
     * @param int $userId     Identifiant de l’utilisateur
     * @return bool true si le véhicule appartient à l’utilisateur, false sinon
     */
    public function isOwnedByUser(int $vehiculeId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM vehicule
            WHERE id = :vid AND utilisateur_id = :uid
            LIMIT 1
        ");
        $stmt->execute(['vid' => $vehiculeId, 'uid' => $userId]);

        return (bool)$stmt->fetchColumn();
    }
}