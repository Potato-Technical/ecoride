<?php
/**
 * View: trajets/edit.php
 * Variables :
 * - $trajet (array)
 * - $errors (array)
 */
use App\Core\Security;
?>
<div class="container my-4">
  <a href="/trajets/<?= (int)$trajet['id_trajet'] ?>" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour</a>

  <h1>Modifier le trajet #<?= (int)$trajet['id_trajet'] ?></h1>
  <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-info text-center my-3">
          <?php echo htmlspecialchars($_SESSION['flash']); ?>
      </div>
      <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <form method="post" action="/trajets/<?= (int)$trajet['id_trajet'] ?>/update" class="row g-3">
    <?= Security::csrfField() ?>

    <!-- Ville de départ -->
    <div class="col-md-6">
      <label for="ville_depart" class="form-label">Ville de départ</label>
      <input type="text" name="ville_depart" id="ville_depart" 
             class="form-control <?= isset($errors['ville_depart']) ? 'is-invalid' : '' ?>" 
             value="<?= Security::h($trajet['ville_depart']) ?>">
      <?php if (isset($errors['ville_depart'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['ville_depart']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Ville d’arrivée -->
    <div class="col-md-6">
      <label for="ville_arrivee" class="form-label">Ville d’arrivée</label>
      <input type="text" name="ville_arrivee" id="ville_arrivee" 
             class="form-control <?= isset($errors['ville_arrivee']) ? 'is-invalid' : '' ?>" 
             value="<?= Security::h($trajet['ville_arrivee']) ?>">
      <?php if (isset($errors['ville_arrivee'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['ville_arrivee']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Date -->
    <div class="col-md-6">
      <label for="date_depart" class="form-label">Date</label>
      <input type="date" name="date_depart" id="date_depart"
             class="form-control <?= isset($errors['date_depart']) ? 'is-invalid' : '' ?>"
             value="<?= Security::h($trajet['date_depart']) ?>">
      <?php if (isset($errors['date_depart'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['date_depart']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Heure -->
    <div class="col-md-6">
      <label for="heure_depart" class="form-label">Heure</label>
      <input type="time" name="heure_depart" id="heure_depart"
             class="form-control <?= isset($errors['heure_depart']) ? 'is-invalid' : '' ?>"
             value="<?= Security::h(substr($trajet['heure_depart'], 0, 5)) ?>">
      <?php if (isset($errors['heure_depart'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['heure_depart']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Nombre de places -->
    <div class="col-md-4">
      <label for="nb_places" class="form-label">Places</label>
      <input type="number" name="nb_places" id="nb_places"
             class="form-control <?= isset($errors['nb_places']) ? 'is-invalid' : '' ?>"
             value="<?= (int)$trajet['nb_places'] ?>">
      <?php if (isset($errors['nb_places'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['nb_places']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Prix -->
    <div class="col-md-4">
      <label for="prix" class="form-label">Prix (credits)</label>
      <input type="number" step="0.01" name="prix" id="prix"
             class="form-control <?= isset($errors['prix']) ? 'is-invalid' : '' ?>"
             value="<?= number_format((float)$trajet['prix'], 2, '.', '') ?>">
      <?php if (isset($errors['prix'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['prix']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Description -->
    <div class="col-md-12">
      <label for="description" class="form-label">Description</label>
      <textarea name="description" id="description" rows="3" class="form-control"><?= Security::h($trajet['description'] ?? '') ?></textarea>
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
  </form>
</div>
