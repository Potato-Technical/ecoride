<?php
namespace App\Core;

/**
 * Classe abstraite Controller
 * Toutes les classes de contrôleurs EcoRide hériteront de celle-ci.
 * Elle fournit la méthode `render()` pour afficher une vue.
 */
abstract class Controller
{
    /**
     * Affiche une vue dans le layout principal
     * @param string $view Nom de la vue (ex: 'home/index')
     * @param array $data Données à transmettre à la vue
     */
    protected function render(string $view, array $data = []): void
    {
        // Convertit les clés du tableau $data en variables utilisables dans la vue
        extract($data);

        // Démarre le tampon de sortie
        ob_start();

        // Inclut la vue demandée (ex: app/Views/home/index.php)
        require_once ROOT . 'app/Views/' . $view . '.php';

        // Stocke le contenu généré dans $content
        $content = ob_get_clean();

        // Inclut le layout principal (ex: base.php)
        require_once ROOT . 'app/Views/layouts/base.php';
    }
}
