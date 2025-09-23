<?php
/**
 * View: errors/404.php
 * Variables possibles :
 * - $title   (string)
 * - $message (string)
 */
?>
<div class="container my-5 text-center">
  <h1 class="display-4 text-danger"><?= \App\Core\Security::h($title ?? "Page non trouvée") ?></h1>
  <p class="lead"><?= \App\Core\Security::h($message ?? "La ressource demandée est introuvable.") ?></p>
  <a href="/" class="btn btn-outline-primary mt-3">&larr; Retour à l’accueil</a>
</div>
