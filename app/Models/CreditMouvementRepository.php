<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class CreditMouvementRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /**
     * Enregistre un mouvement de crédit dans l’historique financier.
     *
     * Rôle :
     * - Centralise tous les débits et crédits utilisateurs
     * - Implémente un modèle de type "ledger" (historique immuable)
     *
     * Règles :
     * - Le montant peut être positif (crédit) ou négatif (débit)
     * - Aucun solde n’est stocké : il est calculé dynamiquement
     *
     * Cas d’usage :
     * - Création de compte (crédit initial)
     * - Réservation de trajet (débit)
     * - Annulation / remboursement
     *
     * @param int      $userId          Identifiant de l’utilisateur impacté
     * @param string   $type            Type de mouvement (creation_compte, debit_reservation, remboursement, etc.)
     * @param int      $montant         Montant du mouvement (positif ou négatif)
     * @param int|null $participationId Participation liée (optionnelle)
     */
    public function add(
        int $userId,
        string $type,
        int $montant,
        ?int $participationId = null
    ): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO credit_mouvement (type, montant, utilisateur_id, participation_id)
             VALUES (:type, :montant, :user_id, :participation_id)'
        );

        $stmt->execute([
            'type'             => $type,
            'montant'          => $montant,
            'user_id'          => $userId,
            'participation_id' => $participationId,
        ]);
    }

    /**
     * Calcule le solde courant d’un utilisateur.
     *
     * Principe :
     * - Le solde est la somme de tous les mouvements de crédit
     * - Aucun cache ni champ dérivé n’est utilisé
     *
     * Sécurité :
     * - Utilisable dans une transaction (ex. réservation)
     * - Peut être précédé d’un verrou FOR UPDATE côté contrôleur
     *
     * @param int $userId Identifiant de l’utilisateur
     * @return int        Solde actuel (en crédits)
     */
    public function getSolde(int $userId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COALESCE(SUM(montant), 0)
             FROM credit_mouvement
             WHERE utilisateur_id = :user_id'
        );
        $stmt->execute(['user_id' => $userId]);

        return (int) $stmt->fetchColumn();
    }
}