<?php
/**
 * View: users/edit.php
 * Données disponibles:
 * - $user: ['nom','prenom','email']
 * - $errors: array de validation éventuelle
 */
use App\Core\Security;
?>
<div class="container my-5" style="max-width:700px">
  <!-- Retour -->
  <a href="/profil" class="btn btn-outline-secondary btn-sm mb-4">&larr; Retour au profil</a>

  <!-- Titre -->
  <h1 class="fw-bold mb-4 text-center">
    <i class="bi bi-person-lines-fill text-success"></i> Modifier mon profil
  </h1>

  <!-- Formulaire -->
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-4">
      <form method="post" action="/profil/update" class="row g-3">
        <?= Security::csrfField() ?>

        <!-- Nom -->
        <div class="col-md-6">
          <label for="nom" class="form-label">Nom</label>
          <input type="text" name="nom" id="nom"
                 class="form-control rounded-3 <?= isset($errors['nom']) ? 'is-invalid' : '' ?>"
                 value="<?= Security::h($user['nom'] ?? '') ?>">
          <?php if (isset($errors['nom'])): ?>
            <div class="invalid-feedback"><?= Security::h($errors['nom']) ?></div>
          <?php endif; ?>
        </div>

        <!-- Prénom -->
        <div class="col-md-6">
          <label for="prenom" class="form-label">Prénom</label>
          <input type="text" name="prenom" id="prenom"
                 class="form-control rounded-3 <?= isset($errors['prenom']) ? 'is-invalid' : '' ?>"
                 value="<?= Security::h($user['prenom'] ?? '') ?>">
          <?php if (isset($errors['prenom'])): ?>
            <div class="invalid-feedback"><?= Security::h($errors['prenom']) ?></div>
          <?php endif; ?>
        </div>

        <!-- Email -->
        <div class="col-md-6">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" id="email"
                 class="form-control rounded-3 <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                 value="<?= Security::h($user['email'] ?? '') ?>">
          <?php if (isset($errors['email'])): ?>
            <div class="invalid-feedback"><?= Security::h($errors['email']) ?></div>
          <?php endif; ?>
        </div>

        <!-- Nouveau mot de passe -->
        <div class="col-md-6">
          <label for="mdp" class="form-label">Nouveau mot de passe (optionnel)</label>
          <input type="password" name="mdp" id="mdp" class="form-control rounded-3">
          <div class="form-text">Laissez vide pour ne pas changer le mot de passe</div>
        </div>

        <!-- Bouton -->
        <div class="col-12 text-center mt-3">
          <button type="submit" class="btn btn-success w-100 rounded-pill py-2">
            <i class="bi bi-check-circle me-1"></i> Enregistrer les modifications
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
