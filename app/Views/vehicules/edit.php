<?php
/**
 * View: vehicules/edit.php
 * Variables :
 * - $vehicule (array)
 * - $errors (array)
 */
use App\Core\Security;
?>
<div class="container my-5" style="max-width: 700px;">
  <!-- Barre de navigation locale -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="btn-group">
      <a href="/profil" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-person-circle"></i> Profil
      </a>
      <a href="/vehicules" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-car-front"></i> Mes véhicules
      </a>
      <a href="/vehicules/<?= (int)$vehicule['id_vehicule'] ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-eye"></i> Voir
      </a>
    </div>
  </div>

  <!-- Titre -->
  <h1 class="fw-bold mb-4 text-center">
    <i class="bi bi-pencil-square text-primary"></i>
    Modifier le véhicule #<?= (int)$vehicule['id_vehicule'] ?>
  </h1>

  <!-- Flash -->
  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center rounded-4 shadow-sm mb-4">
      <?= htmlspecialchars($_SESSION['flash']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- Formulaire -->
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
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

        <!-- Places -->
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
        <div class="col-12 text-center">
          <button type="submit" class="btn btn-success rounded-pill px-4">
            <i class="bi bi-check-circle"></i> Enregistrer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
