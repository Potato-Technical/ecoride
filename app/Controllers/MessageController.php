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
}
