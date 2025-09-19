<?php
namespace App\Models;

use App\Config\Database; // Connexion PDO centralisée
use PDO;

/**
 * TrajetModel - Gestion des trajets
 * Table SQL : trajet
 * Colonnes : id_trajet, ville_depart, ville_arrivee, date_depart, heure_depart, nb_places, prix
 */
class TrajetModel
{
    private PDO $pdo;

    public function __construct()
    {
        // On récupère l’instance PDO depuis Database (singleton)
        $this->pdo = Database::pdo();
    }

    /**
     * Retourne tous les trajets (Index)
     */
    public function all(): array
    {
        $sql = "SELECT id_trajet, ville_depart, ville_arrivee, date_depart, heure_depart, nb_places, prix
                FROM trajet
                ORDER BY date_depart DESC, heure_depart DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Crée un trajet et retourne l'ID inséré (Create/Store)
     * $data attendu :
     * - ville_depart, ville_arrivee, date_depart (Y-m-d),
     * - heure_depart (H:i ou H:i:s), nb_places, prix
     */
    public function create(array $data): int
    {
        // Normalisation de l’heure au format H:i:s
        $heure = isset($data['heure_depart']) 
            ? substr($data['heure_depart'] . ':00:00', 0, 8) 
            : '00:00:00';

        $sql = "INSERT INTO trajet (ville_depart, ville_arrivee, date_depart, heure_depart, nb_places, prix)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['ville_depart'] ?? '',
            $data['ville_arrivee'] ?? '',
            $data['date_depart'] ?? null,
            $heure,
            (int)($data['nb_places'] ?? 0),
            (float)($data['prix'] ?? 0),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Récupère un trajet par son ID (Show)
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT id_trajet, ville_depart, ville_arrivee, date_depart, heure_depart, nb_places, prix
                FROM trajet
                WHERE id_trajet = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
        return $trajet ?: null;
    }

    /**
     * Met à jour un trajet (Edit/Update)
     */
    public function update(int $id, array $data): bool
    {
        $heure = isset($data['heure_depart']) 
            ? substr($data['heure_depart'] . ':00:00', 0, 8) 
            : '00:00:00';

        $sql = "UPDATE trajet
                SET ville_depart = ?, ville_arrivee = ?, date_depart = ?, heure_depart = ?, nb_places = ?, prix = ?
                WHERE id_trajet = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['ville_depart'] ?? '',
            $data['ville_arrivee'] ?? '',
            $data['date_depart'] ?? null,
            $heure,
            (int)($data['nb_places'] ?? 0),
            (float)($data['prix'] ?? 0),
            $id
        ]);
    }

    /**
     * Supprime un trajet (Delete)
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM trajet WHERE id_trajet = ?");
        return $stmt->execute([$id]);
    }
}
