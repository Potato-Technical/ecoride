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

    public function decrementPlaces(int $trajetId): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            'UPDATE trajet SET nb_places = nb_places - 1 WHERE id = :id'
        );
        $stmt->execute(['id' => $trajetId]);
    }
}
