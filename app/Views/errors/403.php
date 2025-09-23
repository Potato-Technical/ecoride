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
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="display-4 text-warning mb-3">
        <?= Security::h($title ?? "Accès refusé") ?>
      </h1>
      <p class="lead mb-4">
        <?= Security::h($message ?? "Vous n’avez pas l’autorisation d’accéder à cette ressource.") ?>
      </p>
      <a href="/" class="btn btn-outline-primary">&larr; Retour à l’accueil</a>
    </div>
  </div>
</div>
