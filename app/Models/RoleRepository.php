<?php

namespace App\Models; // Namespace logique pour les classes d'accès aux données

use App\Core\Database; // Accès à la connexion PDO centralisée
use PDO;               // Constantes PDO (FETCH_ASSOC)

class RoleRepository
{
    /**
     * Récupère un rôle à partir de son libellé
     * Retourne un tableau associatif ou null si absent
     */
    public function findByLibelle(string $libelle): ?array
    {
        // Récupération de la connexion PDO unique
        $pdo = Database::getInstance();

        // Préparation de la requête SQL
        $stmt = $pdo->prepare(
            'SELECT * FROM role WHERE libelle = :libelle'
        );

        // Exécution avec paramètre sécurisé
        $stmt->execute([
            'libelle' => $libelle
        ]);

        // Récupération du résultat sous forme de tableau associatif
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retourne null si aucun rôle trouvé
        return $role ?: null;
    }
}
