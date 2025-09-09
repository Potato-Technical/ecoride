<?php
namespace App\Models;

// On utilise la classe de base Model pour accéder à $this->pdo
use App\Core\Model;

class TrajetModel extends Model
{
    // Nom de la table liée à ce modèle (adapter si besoin)
    protected string $table = 'trajet';

    // Retourne tous les trajets, triés par date décroissante
    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY date_depart DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Retourne un trajet spécifique par son ID
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id_trajet = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null; // null si non trouvé
    }

    // Insère un nouveau trajet en base de données et retourne l’ID généré
    public function create(array $data): int {
        $sql = "INSERT INTO {$this->table} 
            (depart, arrivee, date_depart, places, prix, conducteur_id, description) 
            VALUES (:depart, :arrivee, :date_depart, :places, :prix, :conducteur_id, :description)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'depart'        => $data['depart'],
            'arrivee'       => $data['arrivee'],
            'date_depart'   => $data['date_depart'],
            'places'        => $data['places'],
            'prix'          => $data['prix'],
            'conducteur_id' => $data['conducteur_id'],
            'description'   => $data['description'] ?? null // champ facultatif
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    // Met à jour un trajet existant selon son ID
    public function update(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET depart = :depart, arrivee = :arrivee, date_depart = :date_depart, 
                    places = :places, prix = :prix, conducteur_id = :conducteur_id, 
                    description = :description 
                WHERE id_trajet = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'depart'        => $data['depart'],
            'arrivee'       => $data['arrivee'],
            'date_depart'   => $data['date_depart'],
            'places'        => $data['places'],
            'prix'          => $data['prix'],
            'conducteur_id' => $data['conducteur_id'],
            'description'   => $data['description'] ?? null,
            'id'            => $id
        ]);
    }

    // Supprime un trajet par son ID
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id_trajet = :id");
        return $stmt->execute(['id' => $id]);
    }
}
