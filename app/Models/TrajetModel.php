<?php
namespace App\Models;                        // Namespace logique

use App\Core\Model;                         // On hérite du modèle de base (connexion PDO)

class TrajetModel extends Model              // Classe métier Trajet
{
    public function getAll()                // Méthode pour récupérer tous les trajets
    {
        $sql = "SELECT * FROM trajet";      // Requête simple (à sécuriser plus tard si besoin)
        $stmt = $this->pdo->query($sql);    // Exécution directe (pas de préparation car pas d'entrée utilisateur)
        return $stmt->fetchAll();           // Résultat sous forme de tableau associatif
    }
}
