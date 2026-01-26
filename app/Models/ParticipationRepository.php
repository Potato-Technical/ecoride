<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class ParticipationRepository
{
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
        $participationId = (int) $pdo->lastInsertId();

        $stmt = $pdo->prepare(
            "INSERT INTO credit_mouvement (type, montant, utilisateur_id, participation_id)
            VALUES ('debit_reservation', :montant, :uid, :pid)"
        );
        $stmt->execute([
            'montant' => -abs($prix),
            'uid'     => $userId,
            'pid'     => $participationId,
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
     * Récupère la participation d’un utilisateur sur un trajet (si elle existe).
     *
     * @return array|null  ['id','etat','credits_utilises','trajet_id'] ou null
     */
    public function findOne(int $userId, int $trajetId): ?array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            'SELECT id, etat, credits_utilises, trajet_id
            FROM participation
            WHERE utilisateur_id = :uid AND trajet_id = :tid
            LIMIT 1'
        );

        $stmt->execute([
            'uid' => $userId,
            'tid' => $trajetId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Réactive une réservation précédemment annulée (Option B).
     *
     * Règles métier :
     * - Seul le propriétaire de la réservation peut réactiver
     * - Seules les réservations à l’état "annule" sont réactivables
     * - Une réservation déjà confirmée n’est pas modifiée
     *
     * Effets :
     * - Passe l’état de la participation à "confirme"
     * - Met à jour la date de confirmation
     * - Met à jour le montant de crédits utilisés
     * - Crée un mouvement de débit dans l’historique des crédits
     *
     * Sécurité :
     * - Doit être appelée dans une transaction
     * - Verrouillage FOR UPDATE pour éviter les doubles réactivations
     *
     * @param int $userId   Identifiant de l’utilisateur
     * @param int $trajetId Identifiant du trajet
     * @param int $prix     Montant des crédits à débiter
     * @return bool         true si la réactivation est effectuée, false sinon
     */
    public function reactivate(int $userId, int $trajetId, int $prix): bool
    {
        $pdo = Database::getInstance();

        // Verrouille la ligne participation (anti double-clic / concurrence)
        $stmt = $pdo->prepare(
            'SELECT id, etat
            FROM participation
            WHERE utilisateur_id = :uid AND trajet_id = :tid
            FOR UPDATE'
        );
        $stmt->execute(['uid' => $userId, 'tid' => $trajetId]);
        $p = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$p || $p['etat'] !== 'annule') {
            return false;
        }

        // Réactivation atomique
        $stmt = $pdo->prepare(
            "UPDATE participation
            SET etat = 'confirme',
                confirme_le = NOW(),
                credits_utilises = :prix
            WHERE id = :id AND etat = 'annule'"
        );
        $stmt->execute([
            'prix' => $prix,
            'id'   => (int)$p['id'],
        ]);

        if ($stmt->rowCount() !== 1) {
            return false;
        }

        // Historique crédits : débit (montant négatif recommandé)
        $stmt = $pdo->prepare(
            "INSERT INTO credit_mouvement (type, montant, utilisateur_id, participation_id)
            VALUES ('debit_reservation', :montant, :uid, :pid)"
        );
        $stmt->execute([
            'montant' => -abs($prix),
            'uid'     => $userId,
            'pid'     => (int)$p['id'],
        ]);

        return true;
    }

    /**
     * Annule une réservation utilisateur.
     *
     * Règles métier :
     * - Seul le propriétaire de la réservation peut annuler
     * - Seules les réservations à l’état "confirme" sont annulables
     * - Une réservation déjà annulée est traitée comme un succès (idempotence)
     *
     * Effets :
     * - Passe l’état de la participation à "annule"
     * - Réincrémente le nombre de places du trajet
     * - Crée un mouvement de remboursement dans l’historique des crédits
     *
     * Sécurité :
     * - Transaction SQL
     * - Verrouillage FOR UPDATE pour éviter les doubles annulations
     *
     * @param int $participationId Identifiant de la participation
     * @param int $userId          Identifiant de l’utilisateur courant
     * @return bool                true si l’annulation est effective ou déjà faite, false sinon
     */
    public function cancel(int $participationId, int $userId): bool
    {
        $pdo = Database::getInstance();

        try {
            $pdo->beginTransaction();

            /**
             * 1) Verrouille la participation
             *    → empêche les doubles clics / requêtes concurrentes
             */
            $stmt = $pdo->prepare(
                'SELECT id, etat, credits_utilises, trajet_id
                FROM participation
                WHERE id = :id AND utilisateur_id = :uid
                FOR UPDATE'
            );
            $stmt->execute([
                'id'  => $participationId,
                'uid' => $userId
            ]);

            $participation = $stmt->fetch(PDO::FETCH_ASSOC);

            // Participation inexistante ou ne принадissant pas à l’utilisateur
            if (!$participation) {
                $pdo->rollBack();
                return false;
            }

            /**
             * 2) Idempotence
             *    → déjà annulée = OK (on ne refait pas les effets de bord)
             */
            if ($participation['etat'] === 'annule') {
                $pdo->commit();
                return true;
            }

            /**
             * 3) Règle métier
             *    → seule une participation confirmée peut être annulée
             */
            if ($participation['etat'] !== 'confirme') {
                $pdo->rollBack();
                return false;
            }

            /**
             * 4) Annulation logique sécurisée
             *    → vérifie encore l’état attendu au moment de l’UPDATE
             */
            $stmt = $pdo->prepare(
                "UPDATE participation
                SET etat = 'annule'
                WHERE id = :id
                AND utilisateur_id = :uid
                AND etat = 'confirme'"
            );
            $stmt->execute([
                'id'  => $participationId,
                'uid' => $userId
            ]);

            // Sécurité : une seule ligne doit être impactée
            if ($stmt->rowCount() !== 1) {
                $pdo->rollBack();
                return false;
            }

            /**
             * 5) Réincrémentation des places du trajet
             */
            $stmt = $pdo->prepare(
                'UPDATE trajet
                SET nb_places = nb_places + 1
                WHERE id = :tid'
            );
            $stmt->execute([
                'tid' => (int) $participation['trajet_id']
            ]);

            /**
             * 6) Mouvement de remboursement
             *    → montant positif (historique financier)
             */
            $stmt = $pdo->prepare(
                "INSERT INTO credit_mouvement
                    (type, montant, utilisateur_id, participation_id)
                VALUES
                    ('remboursement', :montant, :uid, :pid)"
            );
            $stmt->execute([
                'montant' => (int) $participation['credits_utilises'],
                'uid'     => $userId,
                'pid'     => (int) $participation['id'],
            ]);

            /**
             * 7) Validation définitive
             */
            $pdo->commit();
            return true;

        } catch (\Throwable $e) {
            // Log interne uniquement (pas d’exposition utilisateur)
            error_log('CANCEL FAIL: ' . $e->getMessage());
            $pdo->rollBack();
            return false;
        }
    }

}
