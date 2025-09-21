<?php
namespace App\Config;

/**
 * Classe MongoSimu
 * Simulation simple de MongoDB avec un fichier JSON local
 */
class MongoSimu {
    private $file;

    /**
     * Constructeur
     * Définit le chemin du fichier JSON servant de "collection"
     */
    public function __construct($file = __DIR__ . "/../../data/stats_trajets.json") {
        $this->file = $file;
    }

    /**
     * Récupération des statistiques
     * Retourne un tableau associatif avec les données du fichier JSON
     */
    public function getStats() {
        if (!file_exists($this->file)) {
            // Valeurs par défaut si le fichier n'existe pas
            return [
                "nb_trajets" => 0,
                "prix_moyen" => 0,
                "trajet_populaire" => "Aucun"
            ];
        }

        // Lecture du fichier
        $json = file_get_contents($this->file);
        return json_decode($json, true);
    }
}
