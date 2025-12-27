<?php

// Ce contrôleur gère les pages "home"
namespace App\Controllers;
use App\Core\Database;

// On hérite du contrôleur parent
use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        // On affiche la vue home/index
        // Le layout sera appliqué automatiquement
        $this->render('home/index', [
            'title' => 'Accueil'
        ]);
    }
}
