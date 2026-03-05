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
     * Retourne la liste des trajets publiés par un utilisateur en tant que chauffeur.
     *
     * Règles :
     * - Filtrage strict par chauffeur_id
     * - Aucun contrôle d’accès ici (doit être assuré par le contrôleur)
     *
     * Usage :
     * - Vue “mes trajets (chauffeur)”
     * - Suivi et gestion des trajets publiés par l’utilisateur
     *
     * @param int $userId Identifiant du chauffeur
     * @return array Liste des trajets (peut être vide)
     */
    public function findByChauffeurId(int $userId): array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            'SELECT
                id,
                lieu_depart,
                lieu_arrivee,
                date_heure_depart,
                prix,
                nb_places,
                places_restantes,
                statut,
                paid_at
            FROM trajet
            WHERE chauffeur_id = :uid
            ORDER BY date_heure_depart DESC'
        );

        $stmt->execute(['uid' => $userId]);

        return $stmt->fetchAll();
    }
    
    /**
     * Décrémente nb_places de façon atomique (anti surréservation).
     *
     * Retourne true si une place a été décrémentée, false sinon.
     * À utiliser en transaction.
     */
    public function decrementPlaces(int $trajetId): bool
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            'UPDATE trajet
            SET places_restantes = places_restantes - 1
            WHERE id = :id AND places_restantes > 0'
        );

        $stmt->execute(['id' => $trajetId]);

        return $stmt->rowCount() === 1;
    }

    /**
     * Crée un trajet.
     */
    public function create(array $data): int
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            'INSERT INTO trajet (
                lieu_depart, lieu_arrivee, date_heure_depart,
                prix, nb_places, places_restantes, statut,
                chauffeur_id, vehicule_id
            ) VALUES (
                :lieu_depart, :lieu_arrivee, :date_heure_depart,
                :prix, :nb_places, :places_restantes, "planifie",
                :chauffeur_id, :vehicule_id
            )'
        );

        $stmt->execute([
            'lieu_depart'       => $data['lieu_depart'],
            'lieu_arrivee'      => $data['lieu_arrivee'],
            'date_heure_depart' => $data['date_heure_depart'],
            'prix'              => $data['prix'],
            'nb_places'         => $data['nb_places'],
            'places_restantes'  => $data['nb_places'],
            'chauffeur_id'      => $data['chauffeur_id'],
            'vehicule_id'       => $data['vehicule_id'],
        ]);

        return (int)$pdo->lastInsertId();
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
            WHERE t.places_restantes > 0
            AND t.statut = 'planifie'
        ";

        $params = [];

        if (($filters['depart'] ?? '') !== '') {
            $sql .= " AND t.lieu_depart LIKE :depart";
            $params['depart'] = '%' . $filters['depart'] . '%';
        }

        if (($filters['arrivee'] ?? '') !== '') {
            $sql .= " AND t.lieu_arrivee LIKE :arrivee";
            $params['arrivee'] = '%' . $filters['arrivee'] . '%';
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

        // Bind paramètres dynamiques (typage explicite + normalisation du nom)
        foreach ($params as $k => $v) {
            $key  = ltrim((string) $k, ':');
            $name = ':' . $key;

            if ($key === 'prix_max') {
                $stmt->bindValue($name, (int) $v, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($name, (string) $v, PDO::PARAM_STR);
            }
        }

        // Bind LIMIT/OFFSET en int (obligatoire)
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verrouille et retourne un trajet appartenant à un chauffeur.
     *
     * Usage :
     * - Annulation d’un trajet par le chauffeur
     * - Opérations critiques nécessitant une cohérence forte
     *
     * Règles :
     * - Le trajet doit appartenir au chauffeur connecté
     * - La ligne est verrouillée (FOR UPDATE) dans le cadre d’une transaction
     *
     * @param int $trajetId    Identifiant du trajet
     * @param int $chauffeurId Identifiant du chauffeur (utilisateur)
     * @return array|null      Données du trajet si ownership valide, null sinon
     */
    public function findOwnedForUpdate(int $trajetId, int $chauffeurId): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            'SELECT *
            FROM trajet
            WHERE id = :id AND chauffeur_id = :cid
            FOR UPDATE'
        );
        $stmt->execute(['id' => $trajetId, 'cid' => $chauffeurId]);
        $t = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $t ?: null;
    }

    /**
     * Met à jour le statut d’un trajet.
     *
     * Usage :
     * - Annulation logique d’un trajet
     * - Évolution du cycle de vie (planifie → annule, etc.)
     *
     * Note :
     * - Aucune validation métier n’est effectuée ici
     * - Les contrôles doivent être faits côté contrôleur
     *
     * @param int    $trajetId Identifiant du trajet
     * @param string $statut   Nouveau statut du trajet
     */
    public function setStatus(int $trajetId, string $statut): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE trajet SET statut = :s WHERE id = :id');
        $stmt->execute(['s' => $statut, 'id' => $trajetId]);
    }

    /**
     * Réincrémente le nombre de places disponibles d’un trajet.
     *
     * Usage :
     * - Annulation d’un trajet (restitution des places)
     * - Annulation groupée des participations confirmées
     *
     * Sécurité :
     * - Ignore les valeurs nulles ou négatives
     * - À appeler dans une transaction si utilisé avec d’autres mises à jour
     *
     * @param int $trajetId Identifiant du trajet
     * @param int $nb       Nombre de places à restituer
     */
    public function incrementPlaces(int $trajetId, int $nb): void
    {
        if ($nb <= 0) return;

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            'UPDATE trajet
            SET places_restantes = places_restantes + :nb
            WHERE id = :id'
        );
        $stmt->execute(['nb' => $nb, 'id' => $trajetId]);
    }

    public function setStart(int $trajetId): bool
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            "UPDATE trajet
            SET statut = 'demarre'
            WHERE id = :id AND statut = 'planifie'"
        );

        $stmt->execute(['id' => $trajetId]);

        return $stmt->rowCount() === 1;
    }

    public function setFinish(int $trajetId): bool
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            "UPDATE trajet
            SET statut = 'termine',
                date_heure_arrivee = NOW()
            WHERE id = :id AND statut = 'demarre'"
        );

        $stmt->execute(['id' => $trajetId]);

        return $stmt->rowCount() === 1;
    }
}
