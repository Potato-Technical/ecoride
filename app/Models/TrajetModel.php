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

    /**
     * Récupère tous les trajets créés par un utilisateur donné
     *
     * @param int $idUser Identifiant de l’utilisateur (conducteur)
     * @return array Liste des trajets appartenant à cet utilisateur
     */
    public function getByUser(int $idUser): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM trajet 
            WHERE id_conducteur = :id
            ORDER BY date_depart DESC
        ");
        $stmt->execute(['id' => $idUser]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère les trajets d’un conducteur avec leurs réservations et passagers
     */
    public function getByUserWithReservations(int $idUser): array
    {
        $sql = "
            SELECT t.id_trajet, t.ville_depart, t.ville_arrivee, t.date_depart, t.heure_depart,
                   r.id_reservation, r.statut AS reservation_statut,
                   u.nom, u.prenom
            FROM trajet t
            LEFT JOIN reservation r ON t.id_trajet = r.id_trajet
            LEFT JOIN utilisateur u ON r.id_user = u.id_user
            WHERE t.id_conducteur = :id
            ORDER BY t.date_depart DESC, r.id_reservation ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idUser]);
        return $stmt->fetchAll();
    }

    /**
     * Recherche des trajets selon filtres (ville_depart, ville_arrivee, date_depart)
     */
    public function search(array $filters): array
    {
        $sql = "SELECT * FROM trajet WHERE 1=1";
        $params = [];

        if (!empty($filters['ville_depart'])) {
            $sql .= " AND ville_depart LIKE :ville_depart";
            $params['ville_depart'] = '%' . $filters['ville_depart'] . '%';
        }
        if (!empty($filters['ville_arrivee'])) {
            $sql .= " AND ville_arrivee LIKE :ville_arrivee";
            $params['ville_arrivee'] = '%' . $filters['ville_arrivee'] . '%';
        }
        if (!empty($filters['date_depart'])) {
            $sql .= " AND date_depart = :date_depart";
            $params['date_depart'] = $filters['date_depart'];
        }

        $sql .= " ORDER BY date_depart ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Récupère une liste de trajets avec limite (pagination simple)
     *
     * @param int $limit Nombre maximum de trajets à récupérer
     * @return array Liste des trajets
     */
    public function getWithLimit(int $limit): array
    {
        $stmt = $this->pdo->prepare("
            SELECT t.*, u.nom, u.prenom
            FROM trajet t
            JOIN utilisateur u ON t.id_conducteur = u.id_user
            ORDER BY t.date_depart ASC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Recherche des trajets avec filtres multiples et limite
     *
     * @param array $filters
     * @param int $limit
     * @return array
     */
    public function searchWithLimit(array $filters, int $limit): array
    {
        $sql = "SELECT t.*, u.nom, u.prenom
                FROM trajet t
                JOIN utilisateur u ON t.id_conducteur = u.id_user
                WHERE 1=1";
        $params = [];

        // Ville de départ
        if (!empty($filters['ville_depart'])) {
            $sql .= " AND t.ville_depart LIKE :ville_depart";
            $params['ville_depart'] = '%' . $filters['ville_depart'] . '%';
        }

        // Ville d’arrivée
        if (!empty($filters['ville_arrivee'])) {
            $sql .= " AND t.ville_arrivee LIKE :ville_arrivee";
            $params['ville_arrivee'] = '%' . $filters['ville_arrivee'] . '%';
        }

        // Date exacte
        if (!empty($filters['date_depart'])) {
            $sql .= " AND t.date_depart = :date_depart";
            $params['date_depart'] = $filters['date_depart'];
        }

        // Filtres multiples
        $orderBy = [];
        if (!empty($filters['filters'])) {
            foreach ($filters['filters'] as $f) {
                switch ($f) {
                    case 'eco':
                        $sql .= " AND t.is_eco = 1";
                        break;
                    case 'prix':
                        $orderBy[] = "t.prix ASC";
                        break;
                }
            }
        }

        // Si aucun tri, tri par date par défaut
        if (!empty($orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $orderBy);
        } else {
            $sql .= " ORDER BY t.date_depart ASC";
        }

        $sql .= " LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

}
