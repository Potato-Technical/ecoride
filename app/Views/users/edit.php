<?php
use App\Core\Security;
?>
<div class="container my-4">
  <a href="/profil" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour au profil</a>

  <h1>Modifier mon profil</h1>

  <form method="post" action="/profil/update" class="row g-3">
    <?= Security::csrfField() ?>

    <!-- Nom -->
    <div class="col-md-6">
      <label for="nom" class="form-label">Nom</label>
      <input type="text" name="nom" id="nom"
             class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>"
             value="<?= htmlspecialchars($user['nom'] ?? '') ?>">
      <?php if (isset($errors['nom'])): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['nom']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Prénom -->
    <div class="col-md-6">
      <label for="prenom" class="form-label">Prénom</label>
      <input type="text" name="prenom" id="prenom"
             class="form-control <?= isset($errors['prenom']) ? 'is-invalid' : '' ?>"
             value="<?= htmlspecialchars($user['prenom'] ?? '') ?>">
      <?php if (isset($errors['prenom'])): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['prenom']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Email -->
    <div class="col-md-6">
      <label for="email" class="form-label">Email</label>
      <input type="email" name="email" id="email"
             class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
             value="<?= htmlspecialchars($user['email'] ?? '') ?>">
      <?php if (isset($errors['email'])): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Nouveau mot de passe -->
    <div class="col-md-6">
      <label for="mdp" class="form-label">Nouveau mot de passe (optionnel)</label>
      <input type="password" name="mdp" id="mdp" class="form-control">
      <div class="form-text">Laissez vide pour ne pas changer le mot de passe</div>
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-success">Enregistrer</button>
    </div>
  </form>
</div>
