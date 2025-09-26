<?php
/**
 * View: errors/forbidden.php
 * Variables :
 * - $title (string)
 * - $message (string)
 */
?>
<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body text-center p-5">
          <h1 class="display-5 fw-bold text-danger mb-4">
            <?= htmlspecialchars($title ?? "Accès interdit") ?>
          </h1>
          <p class="lead mb-4">
            <?= htmlspecialchars($message ?? "Vous n’avez pas les droits pour accéder à cette page.") ?>
          </p>

          <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="/login" class="btn btn-success btn-lg rounded-pill px-5">
              Se connecter
            </a>
            <a href="/register" class="btn btn-outline-secondary btn-lg rounded-pill px-5">
              Créer un compte
            </a>
            <a href="/" class="btn btn-outline-primary btn-lg rounded-pill px-5">
              Retour à l’accueil
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
