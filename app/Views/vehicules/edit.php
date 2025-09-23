<?php
/**
 * View: vehicules/edit.php
 * Variables :
 * - $vehicule (array)
 * - $errors (array)
 */
use App\Core\Security;
?>
<div class="container my-4">
  <!-- Boutons retour -->
  <a href="/profil" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour au profil</a>
  <a href="/vehicules/<?= (int)$vehicule['id_vehicule'] ?>" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour</a>

  <h1>Modifier le véhicule #<?= (int)$vehicule['id_vehicule'] ?></h1>

  <!-- Message flash -->
  <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-info text-center my-3">
          <?= htmlspecialchars($_SESSION['flash']) ?>
      </div>
      <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- Formulaire -->
  <form method="post" action="/vehicules/<?= (int)$vehicule['id_vehicule'] ?>/update" class="row g-3">
    <?= Security::csrfField() ?>

    <!-- Marque -->
    <div class="col-md-6">
      <label for="marque" class="form-label">Marque</label>
      <input type="text" name="marque" id="marque" required
             class="form-control <?= isset($errors['marque']) ? 'is-invalid' : '' ?>"
             value="<?= Security::h($vehicule['marque']) ?>">
      <?php if (isset($errors['marque'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['marque']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Modèle -->
    <div class="col-md-6">
      <label for="modele" class="form-label">Modèle</label>
      <input type="text" name="modele" id="modele" required
             class="form-control <?= isset($errors['modele']) ? 'is-invalid' : '' ?>"
             value="<?= Security::h($vehicule['modele']) ?>">
      <?php if (isset($errors['modele'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['modele']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Immatriculation -->
    <div class="col-md-6">
      <label for="immatriculation" class="form-label">Immatriculation</label>
      <input type="text" name="immatriculation" id="immatriculation" required
             class="form-control <?= isset($errors['immatriculation']) ? 'is-invalid' : '' ?>"
             value="<?= Security::h($vehicule['immatriculation']) ?>">
      <?php if (isset($errors['immatriculation'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['immatriculation']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Nombre de places -->
    <div class="col-md-6">
      <label for="nb_places" class="form-label">Places</label>
      <input type="number" name="nb_places" id="nb_places" min="1" required
             class="form-control <?= isset($errors['nb_places']) ? 'is-invalid' : '' ?>"
             value="<?= (int)$vehicule['nb_places'] ?>">
      <?php if (isset($errors['nb_places'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['nb_places']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Bouton -->
    <div class="col-12">
      <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
  </form>
</div>
