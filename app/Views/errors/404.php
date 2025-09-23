<?php
/**
 * View: errors/404.php
 * Variables possibles :
 * - $title   (string)
 * - $message (string)
 */
use App\Core\Security;
?>
<div class="container my-5 text-center">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1 class="display-4 text-danger mb-3">
        <?= Security::h($title ?? "Page non trouvée") ?>
      </h1>
      <p class="lead mb-4">
        <?= Security::h($message ?? "La ressource demandée est introuvable.") ?>
      </p>
      <a href="/" class="btn btn-outline-primary">&larr; Retour à l’accueil</a>
    </div>
  </div>
</div>
