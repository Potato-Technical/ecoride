<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class VehiculeModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::get();
    }

    /**
     * Récupère tous les véhicules d’un utilisateur
     */
    public function getByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM vehicule 
            WHERE proprietaire = :id
            ORDER BY id_vehicule DESC
        ");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un véhicule par son ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM vehicule 
            WHERE id_vehicule = :id
        ");
        $stmt->execute(['id' => $id]);
        $vehicule = $stmt->fetch(PDO::FETCH_ASSOC);
        return $vehicule ?: null;
    }

    /**
     * Crée un véhicule
     */
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO vehicule (marque, modele, immatriculation, nb_places, proprietaire) 
            VALUES (:marque, :modele, :immatriculation, :nb_places, :proprietaire)
        ");
        $stmt->execute([
            'marque'          => $data['marque'],
            'modele'          => $data['modele'],
            'immatriculation' => $data['immatriculation'],
            'nb_places'       => $data['nb_places'],
            'proprietaire'    => $data['proprietaire']
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Met à jour un véhicule
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE vehicule 
            SET marque = :marque,
                modele = :modele,
                immatriculation = :immatriculation,
                nb_places = :nb_places
            WHERE id_vehicule = :id
        ");
        return $stmt->execute([
            'marque'          => $data['marque'],
            'modele'          => $data['modele'],
            'immatriculation' => $data['immatriculation'],
            'nb_places'       => $data['nb_places'],
            'id'              => $id
        ]);
    }

    /**
     * Supprime un véhicule
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM vehicule WHERE id_vehicule = :id");
        return $stmt->execute(['id' => $id]);
    }
}
