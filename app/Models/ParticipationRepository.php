<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class ParticipationRepository
{
    /**
     * MÉTHODE OBSOLÈTE — NE PLUS UTILISER.
     *
     * Ancien flux “tout-en-un” : vérifs + transaction + écritures SQL.
     * Conservée uniquement à titre de référence.
     *
     * @deprecated Utiliser une transaction explicite côté contrôleur avec :
     *             - ParticipationRepository::create()
     *             - TrajetRepository::decrementPlaces()
     *             - (et la gestion crédit_mouvement dans le contrôleur / repo dédié)
     */
    public function reserve(int $trajetId, int $userId, int $prix): bool
    {
        $pdo = Database::getInstance();

        try {
            // Démarre une transaction (opérations atomiques)
            $pdo->beginTransaction();

            // Vérifie s'il reste des places
            $stmt = $pdo->prepare(
                'SELECT nb_places FROM trajet WHERE id = :id FOR UPDATE'
            );
            $stmt->execute(['id' => $trajetId]);
            $trajet = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$trajet || (int)$trajet['nb_places'] <= 0) {
                $pdo->rollBack();
                return false;
            }

            // Empêche la double réservation
            $stmt = $pdo->prepare(
                'SELECT id FROM participation WHERE utilisateur_id = :uid AND trajet_id = :tid'
            );
            $stmt->execute([
                'uid' => $userId,
                'tid' => $trajetId
            ]);

            if ($stmt->fetch()) {
                $pdo->rollBack();
                return false;
            }

            // Création de la participation
            $stmt = $pdo->prepare(
                'INSERT INTO participation (etat, confirme_le, credits_utilises, utilisateur_id, trajet_id)
                 VALUES ("confirme", NOW(), :credits, :uid, :tid)'
            );
            $stmt->execute([
                'credits' => $prix,
                'uid' => $userId,
                'tid' => $trajetId
            ]);
            // ID réel de la participation
            $participationId = (int) $pdo->lastInsertId();

            // Décrémentation des places
            $stmt = $pdo->prepare(
                'UPDATE trajet
                SET nb_places = nb_places - 1
                WHERE id = :id AND nb_places > 0'
            );
            $stmt->execute(['id' => $trajetId]);

            if ($stmt->rowCount() !== 1) {
                $pdo->rollBack();
                return false;
            }

            // Mouvement de crédit (débit)
            $stmt = $pdo->prepare(
                'INSERT INTO credit_mouvement (type, montant, utilisateur_id, participation_id)
                 VALUES ("debit_reservation", :montant, :uid, :pid)'
            );
            $stmt->execute([
                'montant' => -$prix,
                'uid' => $userId,
                'pid' => $participationId
            ]);

            // Valide la transaction
            $pdo->commit();
            return true;

        } catch (PDOException $e) {
            $pdo->rollBack();
            return false;
        }
    }

    /**
     * Crée une participation confirmée pour un utilisateur sur un trajet donné.
     *
     * Rôle métier :
     * - Insère une nouvelle participation à l’état "confirme"
     * - Enregistre le montant de crédits utilisés
     *
     * Sécurité / cohérence :
     * - Cette méthode n’effectue aucune vérification métier
     * - Les contrôles (places, ownership, doublons) doivent être faits
     *   impérativement côté contrôleur avant l’appel
     *
     * @param int $userId  Identifiant de l’utilisateur
     * @param int $trajetId Identifiant du trajet
     * @param int $prix    Nombre de crédits utilisés
     */
    public function create(int $userId, int $trajetId, int $prix): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            'INSERT INTO participation (etat, confirme_le, credits_utilises, utilisateur_id, trajet_id)
            VALUES ("confirme", NOW(), :credits, :uid, :tid)'
        );
        $stmt->execute([
            'credits' => $prix,
            'uid'     => $userId,
            'tid'     => $trajetId
        ]);
    }

    /**
     * Liste les réservations d’un utilisateur (toutes, y compris annulées).
     */
    public function findByUser(int $userId): array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            'SELECT
                p.id            AS id,
                p.etat          AS etat,
                p.created_at    AS created_at,
                t.id            AS trajet_id,
                t.lieu_depart,
                t.lieu_arrivee,
                t.date_heure_depart,
                t.prix
            FROM participation p
            JOIN trajet t ON t.id = p.trajet_id
            WHERE p.utilisateur_id = :uid
            ORDER BY p.created_at DESC'
        );

        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Indique si l'utilisateur a déjà une participation (quel que soit l'état) pour un trajet.
     */
    public function hasParticipation(int $userId, int $trajetId): bool
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            "SELECT 1 FROM participation
            WHERE utilisateur_id = :uid AND trajet_id = :tid
            AND etat = 'confirme'
            LIMIT 1"
        );
        $stmt->execute([
            'uid' => $userId,
            'tid' => $trajetId
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Indique si l'utilisateur possède déjà une participation
     * (quel que soit l'état) pour un trajet donné.
     */
    public function hasAnyParticipation(int $userId, int $trajetId): bool
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            'SELECT 1
            FROM participation
            WHERE utilisateur_id = :uid
            AND trajet_id = :tid
            LIMIT 1'
        );

        $stmt->execute([
            'uid' => $userId,
            'tid' => $trajetId
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Vérifie si l’utilisateur possède une participation annulée
     * pour un trajet donné.
     *
     * Utilisation :
     * - Permet de détecter un cas de réactivation de réservation
     * - Évite la création d’une nouvelle participation inutile
     *
     * @param int $userId   Identifiant de l’utilisateur
     * @param int $trajetId Identifiant du trajet
     * @return bool True si une participation annulée existe
     */
    public function hasCancelledParticipation(int $userId, int $trajetId): bool
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            'SELECT 1 FROM participation
            WHERE utilisateur_id = :uid
            AND trajet_id = :tid
            AND etat = "annule"
            LIMIT 1'
        );

        $stmt->execute([
            'uid' => $userId,
            'tid' => $trajetId
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Réactive une participation précédemment annulée.
     *
     * Effets :
     * - Change l’état de "annule" à "confirme"
     * - Met à jour la date de confirmation
     *
     * Remarque :
     * - Aucune vérification métier n’est effectuée ici
     * - Les contrôles (places disponibles, ownership) doivent être faits
     *   en amont par le contrôleur
     *
     * @param int $userId   Identifiant de l’utilisateur
     * @param int $trajetId Identifiant du trajet
     */
    public function reactivate(int $userId, int $trajetId): void
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            'UPDATE participation
            SET etat = "confirme", confirme_le = NOW()
            WHERE utilisateur_id = :uid
            AND trajet_id = :tid
            AND etat = "annule"'
        );

        $stmt->execute([
            'uid' => $userId,
            'tid' => $trajetId
        ]);
    }

    /**
     * Annule une réservation.
     * - Réincrémente les places
     * - Crée un mouvement de remboursement
     *
     * @param int $participationId
     * @param int $userId
     * @return bool
     */
    public function cancel(int $participationId, int $userId): bool
    {
        $pdo = Database::getInstance();

        try {
            $pdo->beginTransaction();

            // Récupération de la participation
            $stmt = $pdo->prepare(
                'SELECT * FROM participation WHERE id = :id AND utilisateur_id = :uid'
            );
            $stmt->execute([
                'id' => $participationId,
                'uid' => $userId
            ]);

            $participation = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$participation || $participation['etat'] !== 'confirme') {
                $pdo->rollBack();
                return false;
            }

            // Annulation logique
            $stmt = $pdo->prepare(
                'UPDATE participation SET etat = "annule" WHERE id = :id'
            );
            $stmt->execute(['id' => $participationId]);

            // Réincrémentation des places
            $stmt = $pdo->prepare(
                'UPDATE trajet SET nb_places = nb_places + 1 WHERE id = :id'
            );
            $stmt->execute(['id' => $participation['trajet_id']]);

            // Remboursement
            $stmt = $pdo->prepare(
                'INSERT INTO credit_mouvement (type, montant, utilisateur_id, participation_id)
                 VALUES ("remboursement", :montant, :uid, :pid)'
            );
            $stmt->execute([
                'montant' => abs((int)$participation['credits_utilises']),
                'uid' => $userId,
                'pid' => $participation['id']
            ]);

            $pdo->commit();
            return true;

        } catch (PDOException $e) {
            $pdo->rollBack();
            return false;
        }
    }
}
