<?php

namespace App\Controllers; // Namespace des contrôleurs

use App\Core\Controller; // Contrôleur parent (render, sécurité)

/**
 * Contrôleur des pages publiques.
 *
 * Gère les pages accessibles sans authentification :
 * - Accueil
 * - À propos
 * - Contact
 * - Mentions légales
 * - Conditions générales d'utilisation
 * - Accessibilité
 */
class HomeController extends Controller
{
    /**
     * Page d’accueil de l’application. Accessible à tous les visiteurs. 
     * US 1 : Consultation de la page d’accueil
     */
    public function index(): void
    {
        // Affichage de la page d’accueil
        $this->render('home/index', [
            'title' => 'EcoRide - Covoiturage responsable'
        ]);
    }

    /**
     * Page "À propos". Présentation du concept EcoRide. */
    public function about(): void
    {
        $this->render('home/about', [
            'title' => 'À propos - EcoRide'
        ]);
    }

    /**
     * Page de contact. Informations et moyen de contact. */
    public function contact(): void
    {
        $this->render('home/contact', [
            'title' => 'Contact - EcoRide'
        ]);
    }

    /**
     * Mentions légales. Page statique. */
    public function legalMentions(): void
    {
        $this->render('home/mentions-legales', [
            'title' => 'Mentions légales - EcoRide'
        ]);
    }

    /**
     * Conditions générales d'utilisation. Page statique. */
    public function cgu(): void
    {
        $this->render('home/cgu', [
            'title' => 'CGU - EcoRide'
        ]);
    }

    /**
     * Accessibilité. Page statique. */
    public function accessibilite(): void
    {
        $this->render('home/accessibilite', [
            'title' => 'Accessibilité – EcoRide'
        ]);
    }

}
