<?php

namespace App\Controllers; // Namespace des contrôleurs

use App\Core\Controller;          // Contrôleur parent (render)
use App\Models\UserRepository;    // Repository utilisateur

class HomeController extends Controller
{
    public function index(): void
    {
        // Affichage normal de la page (à réactiver après le test)
        $this->render('home/index', [
            'title' => 'Accueil'
        ]);
    }
}
