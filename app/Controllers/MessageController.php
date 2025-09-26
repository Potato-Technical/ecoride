<?php
namespace App\Controllers;

use App\Core\Controller;

class MessageController extends Controller
{
    public function contact($trajetId)
    {
        // Formulaire de contact simple lié à un trajet
        $this->render('messages/contact', ['trajetId' => $trajetId]);
    }

    public function contactForm()
    {
        $this->render('messages/contact', [
            'title' => 'Contact'
        ]);
    }
    
    public function send()
    {
        $nom     = trim($_POST['nom'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $sujet   = trim($_POST['sujet'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
            $_SESSION['flash'] = "Votre message a bien été envoyé.";
            header("Location: /contact");
            exit;
        }

        $_SESSION['flash'] = "Votre message a bien été envoyé, nous reviendrons vers vous rapidement.";
        header('Location: /contact');
        exit;
    }

}
