<?php
namespace App\Controllers;

use App\Core\Controller;

class VehiculeController extends Controller
{
    public function index()
    {
        // Liste des véhicules de l’utilisateur
        $this->render('vehicules/index');
    }

    public function create()
    {
        // Formulaire ajout véhicule
        $this->render('vehicules/create');
    }

    public function edit($id)
    {
        // Formulaire édition véhicule
        $this->render('vehicules/edit', ['id' => $id]);
    }
}
