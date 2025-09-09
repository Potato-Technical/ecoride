<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\TrajetModel;


class HomeController extends Controller
{
    public function index()
    {
        $this->render('home/index', [
            'titre' => 'Bienvenue sur EcoRide',
            'description' => 'Plateforme de covoiturage éco-responsable'
        ]);
    }
}
