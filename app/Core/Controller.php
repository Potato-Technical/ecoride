<?php

/* Ce fichier contient le contrôleur "parent"
   Tous les contrôleurs vont hériter de celui-ci */
namespace App\Core;

class Controller
{
    protected function render(string $view, array $data = []): void
    {
        // On transforme le tableau en variables utilisables
        // ['title' => 'Accueil'] devient $title = 'Accueil'
        extract($data);

        // On commence à capturer le HTML de la vue
        ob_start();

        // On charge la vue demandée
        // Exemple : home/index → app/Views/home/index.php
        require dirname(__DIR__) . "/Views/{$view}.php";

        // On récupère le HTML généré par la vue
        $content = ob_get_clean();

        // On charge le layout principal
        // Le layout affichera $content
        require dirname(__DIR__) . "/Views/layouts/main.php";
    }
}
