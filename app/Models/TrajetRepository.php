<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class TrajetRepository
{
    /**
     * Récupère un trajet par ID.
     * (Détail trajet)
     */
    public function findById(int $id): ?array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare("SELECT * FROM trajet WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
        return $trajet ?: null;
    }

    /**
     * Décrémente nb_places (à appeler en contexte transactionnel).
     */
    public function decrementPlaces(int $trajetId): void
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare('UPDATE trajet SET nb_places = nb_places - 1 WHERE id = :id');
        $stmt->execute(['id' => $trajetId]);
    }

    /**
     * Crée un trajet.
     */
    public function create(array $data): void
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            '
            INSERT INTO trajet (
                lieu_depart, lieu_arrivee, date_heure_depart,
                prix, nb_places, statut,
                chauffeur_id, vehicule_id
            ) VALUES (
                :lieu_depart, :lieu_arrivee, :date_heure_depart,
                :prix, :nb_places, "planifie",
                :chauffeur_id, :vehicule_id
            )
            '
        );

        $stmt->execute([
            'lieu_depart'       => $data['lieu_depart'],
            'lieu_arrivee'      => $data['lieu_arrivee'],
            'date_heure_depart' => $data['date_heure_depart'],
            'prix'              => $data['prix'],
            'nb_places'         => $data['nb_places'],
            'chauffeur_id'      => $data['chauffeur_id'],
            'vehicule_id'       => $data['vehicule_id'],
        ]);
    }

    /**
     * Recherche paginée des trajets avec filtres optionnels.
     *
     * Responsabilités :
     * - Construire la requête SQL dynamiquement
     * - Appliquer filtres (si fournis)
     * - Appliquer tri
     * - Appliquer LIMIT / OFFSET (pagination SQL)
     *
     * Note :
     * - Ici, pas de CSRF (CSRF = couche HTTP/Controller, pas SQL).
     */
    public function searchWithFiltersPaginated(array $filters, int $limit, int $offset): array
    {
        $pdo = Database::getInstance();

        $sql = "
            SELECT t.*, u.pseudo, v.energie
            FROM trajet t
            JOIN utilisateur u ON u.id = t.chauffeur_id
            JOIN vehicule v ON v.id = t.vehicule_id
            WHERE t.nb_places > 0
              AND t.statut = 'planifie'
        ";

        $params = [];

        // Filtres exacts (à adapter plus tard en LIKE si tu veux)
        if (($filters['depart'] ?? '') !== '') {
            $sql .= " AND t.lieu_depart = :depart";
            $params['depart'] = $filters['depart'];
        }

        if (($filters['arrivee'] ?? '') !== '') {
            $sql .= " AND t.lieu_arrivee = :arrivee";
            $params['arrivee'] = $filters['arrivee'];
        }

        if (($filters['date'] ?? '') !== '') {
            $sql .= " AND DATE(t.date_heure_depart) = :date";
            $params['date'] = $filters['date'];
        }

        if (($filters['prix_max'] ?? '') !== '') {
            $sql .= " AND t.prix <= :prix_max";
            $params['prix_max'] = (int) $filters['prix_max'];
        }

        if (!empty($filters['eco'])) {
            $sql .= " AND v.energie IN ('electrique', 'hybride')";
        }

        // Tri
        switch ($filters['sort'] ?? null) {
            case 'prix':
                $sql .= " ORDER BY t.prix ASC";
                break;
            case 'date':
            default:
                $sql .= " ORDER BY t.date_heure_depart ASC";
                break;
        }

        // Pagination
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);

        // Bind paramètres dynamiques
        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }

        // Bind LIMIT/OFFSET en int (obligatoire)
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
