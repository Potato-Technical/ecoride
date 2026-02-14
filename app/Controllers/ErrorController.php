<?php

namespace App\Controllers;

use App\Core\Controller;

class ErrorController extends Controller
{
    /**
     * Affiche une erreur 400 (requête invalide).
     *
     * Code HTTP : 400
     * Cas : requête mal formée ou paramètres invalides.
     */
    public function badRequest(): void
    {
        http_response_code(400);
        $this->render('errors/400', ['title' => 'Requête invalide']);
    }
    
    /**
     * Affiche une erreur 403 (accès refusé).
     *
     * Code HTTP : 403
     * Cas : utilisateur non autorisé à accéder à la ressource.
     */
    public function forbidden(): void
    {
        http_response_code(403);
        $this->render('errors/403', ['title' => 'Accès refusé']);
    }

    /**
     * Affiche une erreur 404 (page introuvable).
     *
     * Code HTTP : 404
     * Cas : route inexistante ou ressource absente.
     */
    public function notFound(): void
    {
        http_response_code(404);
        $this->render('errors/404', ['title' => 'Page introuvable']);
    }

    /**
     * Affiche une erreur 405 (méthode non autorisée).
     *
     * Code HTTP : 405
     * Cas : méthode HTTP interdite pour cette route.
     */
    public function methodNotAllowed(): void
    {
        http_response_code(405);
        $this->render('errors/405', ['title' => 'Méthode non autorisée']);
    }

    /**
     * Affiche une erreur 500 (erreur serveur).
     *
     * Code HTTP : 500
     * Cas : erreur interne lors du traitement.
     */
    public function serverError(): void
    {
        http_response_code(500);
        $this->render('errors/500', ['title' => 'Erreur serveur']);
    }
}
