<?php
namespace App\Models;

use App\Config\Database;
use App\Core\Model;
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
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
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
    /**
     * Insère un trajet et retourne l'ID (int) du nouvel enregistrement.
     * @param array $data attend: depart, arrivee, date_depart, heure_depart, places, prix
     * @return int id_trajet inséré (>0 si OK, 0 si échec)
     */
    public function create(array $data): int
    {
    // 1) Normaliser l'heure: "HH:MM" -> "HH:MM:SS" (MySQL TIME)
    $heure = $data['heure_depart'] ?? '';
    if (preg_match('/^\d{2}:\d{2}$/', $heure)) {
        $heure .= ':00';
    }
  
    // 2) Conducteur: si pas d'auth encore, fallback id_conducteur = 2 (ou mets celui de ta session)
    $idConducteur = isset($data['id_conducteur']) ? (int)$data['id_conducteur'] : 2;

    // 3) INSERT aligné sur structure.sql (ville_depart, ville_arrivee, nb_places, etc.)
    $sql = "INSERT INTO trajet
            (id_conducteur, ville_depart, ville_arrivee, date_depart, heure_depart, nb_places, prix)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        // Exécute avec les valeurs déjà validées/typées par le contrôleur
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        $idConducteur,                  // id_conducteur (NOT NULL)
        $data['depart']       ?? null,  // -> ville_depart
        $data['arrivee']      ?? null,  // -> ville_arrivee
        $data['date_depart']  ?? null,  // -> date_depart
        $heure                ?: null,  // -> heure_depart (HH:MM:SS)
        (int)($data['places'] ?? 0),    // -> nb_places
        (float)($data['prix'] ?? 0)     // -> prix (DECIMAL)
    ]);

        // ID auto-incrémenté généré par MySQL
        return (int) $this->pdo->lastInsertId();
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

