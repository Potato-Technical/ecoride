<?php
/**
 * View: errors/403.php
 */
use App\Core\Security;
?>
<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body text-center p-5">
          <div class="mb-4">
            <i class="bi bi-shield-lock text-warning" style="font-size:4rem;"></i>
          </div>
          <h1 class="display-5 fw-bold text-warning mb-3">
            <?= Security::h($title ?? "Erreur 403 - Accès interdit") ?>
          </h1>
          <p class="lead mb-4">
            <?= Security::h($message ?? "Vous n’avez pas l’autorisation pour accéder à cette page.") ?>
          </p>
          <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="/login" class="btn btn-success btn-lg rounded-pill px-5">
              <i class="bi bi-box-arrow-in-right me-2"></i> Se connecter
            </a>
            <a href="/register" class="btn btn-outline-secondary btn-lg rounded-pill px-5">
              <i class="bi bi-person-plus me-2"></i> Créer un compte
            </a>
            <a href="/" class="btn btn-outline-primary btn-lg rounded-pill px-5">
              <i class="bi bi-house-door me-2"></i> Retour à l’accueil
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
