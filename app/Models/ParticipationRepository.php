<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class ParticipationRepository
{
    /**
     * Crée une participation confirmée pour un utilisateur.
     * Enregistre aussi le débit de crédits lié à la réservation.
     *
     * @param int $userId   ID de l'utilisateur
     * @param int $trajetId ID du trajet
     * @param int $prix     Crédits utilisés
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

        // Mouvement débit (source de vérité = ledger)
        $creditRepo = new CreditMouvementRepository();
        $creditRepo->add(
            $userId,
            'debit_reservation',
            -abs($prix),
            $participationId,
            $trajetId
        );
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
        return $this->hasAnyParticipation($userId, $trajetId);
    }

    public function hasConfirmedParticipation(int $userId, int $trajetId): bool
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            "SELECT 1
            FROM participation
            WHERE utilisateur_id = :uid
            AND trajet_id = :tid
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
     * Annule une réservation utilisateur.
     *
     * - Vérifie que la participation appartient à l'utilisateur
     * - Vérifie que la réservation est confirmée
     * - Vérifie que le trajet est encore planifié
     * - Passe la participation à "annule"
     * - Remet une place disponible sur le trajet
     * - Ajoute un mouvement de remboursement dans credit_mouvement
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
             *    Empêche double clic ou requêtes concurrentes
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

            // Participation inexistante ou ne appartenant pas à l’utilisateur
            if (!$participation) {
                $pdo->rollBack();
                return false;
            }

            /**
             * 2) Verrouille le trajet associé
             *    Permet de contrôler le statut et sécuriser la mise à jour des places
             */
            $stmt = $pdo->prepare(
                'SELECT id, statut, nb_places, places_restantes
                FROM trajet
                WHERE id = :tid
                FOR UPDATE'
            );
            $stmt->execute([
                'tid' => (int)$participation['trajet_id']
            ]);

            $trajet = $stmt->fetch(PDO::FETCH_ASSOC);

            // Trajet inexistant ou plus annulable
            if (!$trajet || $trajet['statut'] !== 'planifie') {
                $pdo->rollBack();
                return false;
            }

            /**
             * 3) Idempotence
             *    Déjà annulée → succès sans répéter les effets de bord
             */
            if ($participation['etat'] === 'annule') {
                $pdo->commit();
                return true;
            }

            /**
             * 4) Règle métier
             *    Seule une participation confirmée peut être annulée
             */
            if ($participation['etat'] !== 'confirme') {
                $pdo->rollBack();
                return false;
            }

            /**
             * 5) Annulation logique sécurisée
             */
            $stmt = $pdo->prepare(
                "UPDATE participation
                SET etat = 'annule',
                    confirme_le = NULL
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
             * 6) Réincrémentation des places du trajet
             *    La ligne est déjà verrouillée → aucune course possible
             */
            $stmt = $pdo->prepare(
                'UPDATE trajet
                SET places_restantes = LEAST(nb_places, places_restantes + 1)
                WHERE id = :tid'
            );
            $stmt->execute([
                'tid' => (int)$participation['trajet_id']
            ]);

            if ($stmt->rowCount() !== 1) {
                $pdo->rollBack();
                return false;
            }

            /**
             * 7) Mouvement de remboursement (ledger)
             *    → montant positif
             *    → lié à la participation ET au trajet (audit complet)
             */
            $creditRepo = new CreditMouvementRepository();
            $creditRepo->add(
                $userId,
                'remboursement',
                (int)$participation['credits_utilises'],
                (int)$participation['id'],
                (int)$participation['trajet_id']
            );

            /**
             * 8) Validation finale
             */
            $pdo->commit();
            return true;

        } catch (\Throwable $e) {
            error_log('CANCEL FAIL: ' . $e->getMessage());

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return false;
        }
    }

    /**
     * Annule toutes les participations confirmées d’un trajet et rembourse les passagers.
     *
     * Objectif :
     * - Ne rembourser que les participations réellement annulées
     * - Éviter tout double remboursement en cas d’appel concurrent ou répété
     *
     * Règles métier :
     * - Seules les participations à l’état "confirme" sont concernées
     * - Une participation déjà annulée est ignorée
     *
     * Fonctionnement :
     * - Sélectionne et verrouille (FOR UPDATE) les participations confirmées du trajet
     * - Annule chaque participation individuellement
     * - Crée un mouvement de remboursement uniquement si l’annulation a effectivement eu lieu
     *
     * Sécurité :
     * - Doit être appelée à l’intérieur d’une transaction SQL
     * - Verrouillage pessimiste pour éviter les courses critiques
     *
     * @param int $trajetId Identifiant du trajet à annuler
     * @return int Nombre de participations effectivement annulées (et remboursées)
     */
    public function cancelAllConfirmedByTrajet(int $trajetId): int
    {
        $pdo = Database::getInstance();

        /**
         * 1) Sélection + verrouillage des participations confirmées
         *    → empêche double annulation / double remboursement
         */
        $stmt = $pdo->prepare(
            "SELECT id, utilisateur_id, credits_utilises
             FROM participation
             WHERE trajet_id = :tid
               AND etat = 'confirme'
             FOR UPDATE"
        );
        $stmt->execute(['tid' => $trajetId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) {
            return 0;
        }

        /**
         * 2) Préparation des requêtes
         */
        $upd = $pdo->prepare(
            "UPDATE participation
             SET etat = 'annule',
                 confirme_le = NULL
             WHERE id = :id
               AND etat = 'confirme'"
        );

        $ins = $pdo->prepare(
            "INSERT INTO credit_mouvement
                (type, montant, utilisateur_id, participation_id, trajet_id)
            VALUES
                ('remboursement', :montant, :uid, :pid, :tid)"
        );

        $count = 0;

        /**
         * 3) Annulation + remboursement contrôlé
         */
        foreach ($rows as $p) {
            $upd->execute(['id' => (int)$p['id']]);

            // Sécurité : on rembourse uniquement si l’UPDATE a bien modifié la ligne
            if ($upd->rowCount() === 1) {
                $ins->execute([
                    'montant' => (int)$p['credits_utilises'], // montant positif
                    'uid'     => (int)$p['utilisateur_id'],
                    'pid'     => (int)$p['id'],
                    'tid'     => $trajetId,
                ]);
                $count++;
            }
        }

        /**
         * 4) Retourne le nombre de participations effectivement annulées
         */
        return $count;
    }

    public function findByUserWithTrajetStatus(int $userId): array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            'SELECT
                p.id            AS participation_id,
                p.etat          AS etat,
                p.created_at    AS created_at,
                p.confirme_le   AS confirme_le,
                p.credits_utilises,
                t.id            AS trajet_id,
                t.lieu_depart,
                t.lieu_arrivee,
                t.date_heure_depart,
                t.prix,
                t.statut        AS trajet_statut,
                t.chauffeur_id  AS chauffeur_id,
                u.pseudo        AS chauffeur_pseudo
            FROM participation p
            JOIN trajet t ON t.id = p.trajet_id
            JOIN utilisateur u ON u.id = t.chauffeur_id
            WHERE p.utilisateur_id = :uid
            ORDER BY p.created_at DESC'
        );

        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findConfirmedPassengersByTrajetForUpdate(int $trajetId): array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            "SELECT
                p.id AS participation_id,
                p.utilisateur_id,
                p.credits_utilises,
                u.email,
                u.pseudo
            FROM participation p
            JOIN utilisateur u ON u.id = p.utilisateur_id
            WHERE p.trajet_id = :tid
            AND p.etat = 'confirme'
            FOR UPDATE"
        );

        $stmt->execute(['tid' => $trajetId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countConfirmedByTrajet(int $trajetId): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "SELECT COUNT(*)
            FROM participation
            WHERE trajet_id = :tid AND etat = 'confirme'"
        );
        $stmt->execute(['tid' => $trajetId]);
        return (int)$stmt->fetchColumn();
    }

    public function sumCreditsConfirmedByTrajet(int $trajetId): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "SELECT COALESCE(SUM(credits_utilises),0)
            FROM participation
            WHERE trajet_id = :tid AND etat = 'confirme'"
        );
        $stmt->execute(['tid' => $trajetId]);
        return (int)$stmt->fetchColumn();
    }

}
