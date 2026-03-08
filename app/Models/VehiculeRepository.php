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
                fumeur,
                animaux,
                preferences_libres,
                utilisateur_id
            ) VALUES (
                :immatriculation,
                :date_premiere_immatriculation,
                :modele,
                :marque,
                :couleur,
                :energie,
                :fumeur,
                :animaux,
                :preferences_libres,
                :utilisateur_id
            )
        ");

        $prefs = ($data['preferences_libres'] ?? '') !== ''
            ? $data['preferences_libres']
            : null;

        $stmt->execute([
            'immatriculation'               => $data['immatriculation'],
            'date_premiere_immatriculation' => $data['date_premiere_immatriculation'],
            'modele'                        => $data['modele'],
            'marque'                        => $data['marque'],
            'couleur'                       => $data['couleur'],
            'energie'                       => $data['energie'],
            'fumeur'                        => (int)$data['fumeur'],
            'animaux'                       => (int)$data['animaux'],
            'preferences_libres'            => $prefs,
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
            SELECT 
                id,
                immatriculation,
                marque,
                modele,
                couleur,
                energie,
                date_premiere_immatriculation,
                fumeur,
                animaux,
                preferences_libres
            FROM vehicule
            WHERE utilisateur_id = :uid
            ORDER BY id ASC
        ");

        $stmt->execute(['uid' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
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

    /**
     * Récupère un véhicule par id uniquement s’il appartient à l’utilisateur.
     *
     * Objectif :
     * - Centraliser le contrôle d’ownership côté repository
     * - Éviter qu’un utilisateur accède/modifie un véhicule d’un autre compte
     *
     * @param int $id     Identifiant du véhicule
     * @param int $userId Identifiant de l’utilisateur propriétaire
     * @return array|null Véhicule complet (ligne) ou null si inexistant / non possédé
     */
    public function findOwnedById(int $id, int $userId): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            'SELECT * FROM vehicule WHERE id = :id AND utilisateur_id = :uid'
        );
        $stmt->execute(['id' => $id, 'uid' => $userId]);
        $v = $stmt->fetch(PDO::FETCH_ASSOC);
        return $v ?: null;
    }

    /**
     * Met à jour un véhicule uniquement si l’utilisateur en est propriétaire.
     *
     * Règles :
     * - Filtrage strict sur (id, utilisateur_id)
     * - La validation des champs (format, contraintes métier) est faite côté contrôleur
     *
     * Détail :
     * - preferences_libres : stocke NULL si champ vide (évite les chaînes vides en base)
     *
     * @param int   $id     Identifiant du véhicule
     * @param int   $userId Identifiant de l’utilisateur propriétaire
     * @param array $data   Données du véhicule (champs éditables)
     * @return void
     */
    public function updateOwned(int $id, int $userId, array $data): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            'UPDATE vehicule
            SET immatriculation = :immatriculation,
                date_premiere_immatriculation = :date_premiere_immatriculation,
                marque = :marque,
                modele = :modele,
                couleur = :couleur,
                energie = :energie,
                fumeur = :fumeur,
                animaux = :animaux,
                preferences_libres = :preferences_libres
            WHERE id = :id AND utilisateur_id = :uid'
        );

        $prefs = $data['preferences_libres'] !== '' ? $data['preferences_libres'] : null;

        $stmt->execute([
            'immatriculation'               => $data['immatriculation'],
            'date_premiere_immatriculation' => $data['date_premiere_immatriculation'],
            'marque'                        => $data['marque'],
            'modele'                        => $data['modele'],
            'couleur'                       => $data['couleur'],
            'energie'                       => $data['energie'],
            'fumeur'                        => (int)$data['fumeur'],
            'animaux'                      => (int)$data['animaux'],
            'preferences_libres'            => $prefs,
            'id'                            => $id,
            'uid'                           => $userId,
        ]);
    }

    /**
     * Supprime un véhicule uniquement si l’utilisateur en est propriétaire.
     *
     * Règles :
     * - Filtrage strict sur (id, utilisateur_id)
     * - Retourne false si la suppression est impossible
     *
     * Cas d’échec typique :
     * - Véhicule référencé par un trajet (trajet.vehicule_id) → contrainte FK
     *
     * @param int $id     Identifiant du véhicule
     * @param int $userId Identifiant du propriétaire
     * @return bool true si supprimé, false sinon (dont contrainte FK)
     */
    public function deleteOwned(int $id, int $userId): bool
    {
        $pdo = Database::getInstance();
        try {
            $stmt = $pdo->prepare('DELETE FROM vehicule WHERE id = :id AND utilisateur_id = :uid');
            $stmt->execute(['id' => $id, 'uid' => $userId]);
            return $stmt->rowCount() === 1;
        } catch (\Throwable $e) {
            // Échec probable : contrainte FK (trajet.vehicule_id)
            return false;
        }
    }
}