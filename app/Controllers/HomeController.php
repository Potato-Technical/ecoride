<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * HomeController - page d'accueil
 */
class HomeController extends Controller
{
    public function index(): void
    {
        // je rends la vue home/index (si tu n'as pas encore la vue, on peut afficher du texte)
        // fallback simple si vue absente :
        $viewPath = ROOT . 'app' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'index.php';
        if (file_exists($viewPath)) {
            $this->render('home/index', [
                'titre' => 'Bienvenue sur EcoRide',
                'description' => 'Plateforme de covoiturage éco-responsable'
            ]);
            return;
        }

        // fallback : simple HTML
        echo "<h1>EcoRide</h1>\n";
        echo "<p>Bienvenue sur EcoRide</p>\n";
        echo "<p>Plateforme de covoiturage éco-responsable</p>\n";
    }
}
