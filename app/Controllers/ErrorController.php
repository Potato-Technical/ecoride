<?php

namespace App\Controllers;

use App\Core\Controller;

class ErrorController extends Controller
{
    /**
     * Gère les erreurs 404 (ressource introuvable).
     *
     * Usage :
     * - Route inexistante
     * - Ressource supprimée ou inaccessible
     *
     * Effets :
     * - Définit le code HTTP 404
     * - Affiche une page d’erreur dédiée
     */
    public function notFound(): void
    {
        http_response_code(404);
        $this->render('errors/404', ['title' => 'Page introuvable']);
    }
}