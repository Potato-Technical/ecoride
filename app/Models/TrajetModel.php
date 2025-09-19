<?php
namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * TrajetModel - CRUD pour table 'trajet' (singulier)
 * j'utilise Database::pdo() pour la connexion
 */
class TrajetModel
{
    private PDO $pdo;

    public function __construct()
    {
        // je récupère la connexion PDO centralisée
        $this->pdo = Database::pdo();
    }

    // retourne tous les trajets
    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM trajet ORDER BY id_trajet DESC");
        return $stmt->fetchAll();
    }

    // retourne un trajet par id
    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM trajet WHERE id_trajet = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res === false ? null : $res;
    }

    // création
    public function create(array $data): bool
    {
        $sql = "INSERT INTO trajet (depart, arrivee, date_depart, heure_depart, places, prix)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['depart'] ?? null,
            $data['arrivee'] ?? null,
            $data['date_depart'] ?? null,
            $data['heure_depart'] ?? null,
            $data['places'] ?? 0,
            $data['prix'] ?? 0
        ]);
    }

    // mise à jour
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE trajet SET depart = ?, arrivee = ?, date_depart = ?, heure_depart = ?, places = ?, prix = ? WHERE id_trajet = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['depart'] ?? null,
            $data['arrivee'] ?? null,
            $data['date_depart'] ?? null,
            $data['heure_depart'] ?? null,
            $data['places'] ?? 0,
            $data['prix'] ?? 0,
            $id
        ]);
    }

    // suppression
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM trajet WHERE id_trajet = ?");
        return $stmt->execute([$id]);
    }
}
