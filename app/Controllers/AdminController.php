<?php

namespace App\Controllers;

use App\Core\Controller;

class AdminController extends Controller
{
    /**
     * Tableau de bord administrateur.
     *
     * Accès réservé aux administrateurs.
     */
    public function index(): void
    {
        $this->requireRole('administrateur');

        $this->render('admin/index', [
            'title' => 'Administration'
        ]);
    }
}
