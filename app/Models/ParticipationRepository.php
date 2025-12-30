<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class ParticipationRepository
{
    /**
     * Réserve un trajet pour un utilisateur.
     * - Vérifie qu'il reste des places
     * - Empêche une double réservation
     * - Décrémente le nombre de places
     * - Crée le mouvement de crédit (débit)
     *
     * @param int $trajetId
     * @param int $userId
     * @param int $prix
     * @return bool
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
                 VALUES ("confirmé", NOW(), :credits, :uid, :tid)'
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
                'UPDATE trajet SET nb_places = nb_places - 1 WHERE id = :id'
            );
            $stmt->execute(['id' => $trajetId]);

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

            if (!$participation || $participation['etat'] !== 'confirmé') {
                $pdo->rollBack();
                return false;
            }

            // Annulation logique
            $stmt = $pdo->prepare(
                'UPDATE participation SET etat = "annulé" WHERE id = :id'
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
