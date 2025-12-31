<?php

namespace App\Controllers; // Namespace des contrôleurs

use App\Core\Controller; // Contrôleur parent (render, sécurité)

class HomeController extends Controller
{
    /**
     * Page d’accueil de l’application.
     *
     * Accès restreint aux administrateurs.
     * (peut servir de dashboard ultérieur)
     *
     * US 13 : Espace administrateur
     */
    public function index(): void
    {
        // Affichage de la page d’accueil
        $this->render('home/index', [
            'title' => 'Accueil'
        ]);
    }
}
