<?php
namespace App\Models;

use App\Config\Database; // pour utiliser Database::get()
use PDO;

/**
 * TrajetModel
 * Gère les interactions avec la table `trajet`
 */
class TrajetModel
{
    private PDO $pdo;

    public function __construct()
    {
        // Ouverture de la connexion BDD via Database::get()
        $this->pdo = Database::get();
    }

    /**
     * Récupère tous les trajets (liste)
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM trajet ORDER BY date_depart ASC");
        return $stmt->fetchAll();
    }

    /**
     * Récupère un trajet par son id
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM trajet WHERE id_trajet = :id");
        $stmt->execute(['id' => $id]);
        $trajet = $stmt->fetch();

        return $trajet ?: null;
    }

    /**
     * Ajoute un trajet (création)
     * - Vérifie que l'id_conducteur est fourni
     */
    public function create(array $data): bool
    {
        if (empty($data['id_conducteur'])) {
            throw new \InvalidArgumentException("Le champ id_conducteur est obligatoire.");
        }

        $sql = "INSERT INTO trajet 
                (id_conducteur, ville_depart, ville_arrivee, date_depart, heure_depart, nb_places, description, prix, is_eco) 
                VALUES 
                (:id_conducteur, :ville_depart, :ville_arrivee, :date_depart, :heure_depart, :nb_places, :description, :prix, :is_eco)";
        
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id_conducteur' => $data['id_conducteur'],
            'ville_depart'  => $data['ville_depart'],
            'ville_arrivee' => $data['ville_arrivee'],
            'date_depart'   => $data['date_depart'],
            'heure_depart'  => $data['heure_depart'],
            'nb_places'     => $data['nb_places'],
            'description'   => $data['description'] ?? null,
            'prix'          => $data['prix'],
            'is_eco'        => $data['is_eco'] ?? 0,
        ]);
    }

    /**
     * Met à jour un trajet (modification)
     * - Vérifie que l'id_conducteur est fourni
     * - L'admin est déjà validé côté contrôleur
     */
    public function update(int $id, array $data): bool
    {
        if (empty($data['id_conducteur'])) {
            throw new \InvalidArgumentException("Le champ id_conducteur est obligatoire.");
        }

        $sql = "UPDATE trajet SET
                    ville_depart  = :ville_depart,
                    ville_arrivee = :ville_arrivee,
                    date_depart   = :date_depart,
                    heure_depart  = :heure_depart,
                    nb_places     = :nb_places,
                    description   = :description,
                    prix          = :prix,
                    is_eco        = :is_eco
                WHERE id_trajet = :id
                  AND id_conducteur = :id_conducteur";
        // condition id_conducteur → empêche qu'un utilisateur modifie un trajet qui n'est pas le sien

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id'            => $id,
            'id_conducteur' => $data['id_conducteur'],
            'ville_depart'  => $data['ville_depart'],
            'ville_arrivee' => $data['ville_arrivee'],
            'date_depart'   => $data['date_depart'],
            'heure_depart'  => $data['heure_depart'],
            'nb_places'     => $data['nb_places'],
            'description'   => $data['description'] ?? null,
            'prix'          => $data['prix'],
            'is_eco'        => $data['is_eco'] ?? 0,
        ]);
    }

    /**
     * Supprime un trajet
     * - La vérification conducteur/admin se fait côté contrôleur
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM trajet WHERE id_trajet = :id");
        return $stmt->execute(['id' => $id]);
    }
}
