<?php
namespace App\Controllers;

use App\Core\Controller;

class StaticController extends Controller
{
    /**
     * Page Mentions légales
     */
    public function mentions()
    {
        $this->render('static/mentions');
    }

    /**
     * Page Conditions Générales d’Utilisation
     */
    public function cgu()
    {
        $this->render('static/cgu');
    }

    /**
     * Page Accessibilité
     */
    public function access()
    {
        $this->render('static/accessibilite');
    }
}
