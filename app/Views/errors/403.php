<?php
/**
 * View: errors/403.php
 * Variables possibles :
 * - $title   (string)
 * - $message (string)
 */
use App\Core\Security;
?>
<div class="container my-5 text-center">
  <h1 class="display-4 text-warning"><?= Security::h($title ?? "Accès refusé") ?></h1>
  <p class="lead"><?= Security::h($message ?? "Vous n’avez pas l’autorisation d’accéder à cette ressource.") ?></p>
  <a href="/" class="btn btn-outline-primary mt-3">&larr; Retour à l’accueil</a>
</div>
