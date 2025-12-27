<?php

namespace App\Controllers; // Namespace des contrôleurs

use App\Core\Controller; // Contrôleur parent (render, sécurité)

class HomeController extends Controller
{
    public function index(): void
    {
        // Accès réservé aux utilisateurs connectés
        $this->requireAuth();

        // Affichage de la page d’accueil
        $this->render('home/index', [
            'title' => 'Accueil'
        ]);
    }
}
