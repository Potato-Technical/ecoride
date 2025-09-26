<?php
/**
 * View: auth/login.php
 */
use App\Core\Security;
?>
<div class="container my-5" style="max-width:420px;">
  <!-- Titre -->
  <h1 class="fw-bold mb-4 text-center">
    <i class="bi bi-box-arrow-in-right text-primary"></i> Se connecter
  </h1>

  <!-- Flash -->
  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center rounded-4 shadow-sm mb-3">
      <?= Security::h($_SESSION['flash']); ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- Formulaire -->
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-4">
      <form method="post" action="/login" class="row g-3">
        <?= method_exists(Security::class, 'csrfField') ? Security::csrfField() : '' ?>

        <!-- Email -->
        <div class="col-12">
          <label for="email" class="form-label">Email</label>
          <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
        </div>

        <!-- Mot de passe -->
        <div class="col-12">
          <label for="password" class="form-label">Mot de passe</label>
          <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
        </div>

        <!-- Bouton -->
        <div class="col-12">
          <button type="submit" class="btn btn-primary w-100 rounded-pill">
            <i class="bi bi-box-arrow-in-right"></i> Connexion
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Lien inscription -->
  <div class="text-center mt-3">
    <a href="/register" class="small text-decoration-none">
      <i class="bi bi-person-plus"></i> Pas encore inscrit ? Créer un compte
    </a>
  </div>
</div>
