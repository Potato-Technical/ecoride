<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class TrajetRepository
{
    /**
     * Recherche des trajets disponibles selon les critères utilisateur.
     * - Ville de départ
     * - Ville d’arrivée
     * - Date de départ
     * - Trajets planifiés avec au moins une place disponible
     *
     * @param string $depart  Ville de départ
     * @param string $arrivee Ville d’arrivée
     * @param string $date    Date du trajet (YYYY-MM-DD)
     * @return array Liste des trajets correspondants
     */
    public function search(string $depart, string $arrivee, string $date): array
    {
        // Connexion à la base de données
        $pdo = Database::getInstance();

        // Requête de recherche filtrée
        $stmt = $pdo->prepare(
            "
            SELECT t.*, u.pseudo, v.energie
            FROM trajet t
            JOIN utilisateur u ON u.id = t.chauffeur_id
            JOIN vehicule v ON v.id = t.vehicule_id
            WHERE t.lieu_depart = :depart
              AND t.lieu_arrivee = :arrivee
              AND DATE(t.date_heure_depart) = :date
              AND t.nb_places > 0
              AND t.statut = 'planifie'
            ORDER BY t.date_heure_depart ASC
            "
        );

        // Exécution sécurisée avec paramètres liés
        $stmt->execute([
            'depart'  => $depart,
            'arrivee' => $arrivee,
            'date'    => $date,
        ]);

        // Retourne tous les trajets trouvés
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les trajets planifiés et disponibles.
     * Utilisé pour l’affichage par défaut de /trajets.
     *
     * @return array Liste des trajets disponibles
     */
    public function findAllAvailable(): array
    {
        // Connexion à la base de données
        $pdo = Database::getInstance();

        // Requête simple sans critères utilisateur
        $stmt = $pdo->query(
            "
            SELECT t.*, u.pseudo, v.energie
            FROM trajet t
            JOIN utilisateur u ON u.id = t.chauffeur_id
            JOIN vehicule v ON v.id = t.vehicule_id
            WHERE t.nb_places > 0
              AND t.statut = 'planifie'
            ORDER BY t.date_heure_depart ASC
            "
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un trajet à partir de son identifiant.
     *
     * @param int $id Identifiant du trajet
     * @return array|null Données du trajet ou null si inexistant
     */
    public function findById(int $id): ?array
    {
        // Connexion à la base de données
        $pdo = Database::getInstance();

        // Requête de récupération d’un trajet précis
        $stmt = $pdo->prepare(
            "SELECT * FROM trajet WHERE id = :id"
        );

        $stmt->execute(['id' => $id]);

        $trajet = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retourne null si aucun résultat
        return $trajet ?: null;
    }

    /**
     * Décrémente le nombre de places disponibles pour un trajet.
     *
     * Utilisation :
     * - Lors d’une réservation confirmée
     * - Appelée uniquement dans un contexte transactionnel
     *   (contrôleur ou service métier)
     *
     * Hypothèses :
     * - Le trajet existe
     * - Le nombre de places a déjà été validé (> 0)
     *
     * @param int $trajetId Identifiant du trajet
     */
    public function decrementPlaces(int $trajetId): void
    {
        // Connexion à la base de données
        $pdo = Database::getInstance();

        // Mise à jour atomique du nombre de places
        $stmt = $pdo->prepare(
            'UPDATE trajet SET nb_places = nb_places - 1 WHERE id = :id'
        );

        // Exécution sécurisée
        $stmt->execute(['id' => $trajetId]);
    }

    /**
     * Crée un nouveau trajet (covoiturage).
     *
     * Responsabilités :
     * - Insérer un trajet planifié en base
     * - Associer le conducteur (utilisateur connecté)
     * - Initialiser le statut métier à "planifie"
     *
     * La validation des données et la sécurité (CSRF, droits)
     * sont gérées côté contrôleur.
     *
     * @param array $data Données du trajet
     */
    public function create(array $data): void
    {
        // Connexion à la base de données
        $pdo = Database::getInstance();

        // Requête d’insertion sécurisée
        $stmt = $pdo->prepare(
            '
            INSERT INTO trajet (
                lieu_depart,
                lieu_arrivee,
                date_heure_depart,
                prix,
                nb_places,
                statut,
                chauffeur_id,
                vehicule_id
            ) VALUES (
                :lieu_depart,
                :lieu_arrivee,
                :date_heure_depart,
                :prix,
                :nb_places,
                "planifie",
                :chauffeur_id,
                :vehicule_id
            )
            '
        );

        // Exécution avec paramètres liés
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
}
