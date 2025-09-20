<?php
/**
 * View: trajets/create.php
 * Variables possibles :
 * - $errors (array)
 * - $old (array) : valeurs précédentes si erreur
 */
use App\Core\Security;
?>
<div class="container my-4">
  <a href="/trajets" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour à la liste</a>

  <h1>Proposer un trajet</h1>

  <form method="post" action="/trajets/store" class="row g-3">
    <?= Security::csrfField() ?>
    
    <!-- Ville de départ -->
    <div class="col-md-6">
      <label for="ville_depart" class="form-label">Ville de départ</label>
      <input type="text" name="ville_depart" id="ville_depart"
             class="form-control <?= isset($errors['ville_depart']) ? 'is-invalid' : '' ?>"
             value="<?= Security::h($old['ville_depart'] ?? '') ?>">
      <?php if (isset($errors['ville_depart'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['ville_depart']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Ville d’arrivée -->
    <div class="col-md-6">
      <label for="ville_arrivee" class="form-label">Ville d’arrivée</label>
      <input type="text" name="ville_arrivee" id="ville_arrivee"
             class="form-control <?= isset($errors['ville_arrivee']) ? 'is-invalid' : '' ?>"
             value="<?= Security::h($old['ville_arrivee'] ?? '') ?>">
      <?php if (isset($errors['ville_arrivee'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['ville_arrivee']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Date -->
    <div class="col-md-6">
      <label for="date_depart" class="form-label">Date</label>
      <input type="date" name="date_depart" id="date_depart"
             class="form-control <?= isset($errors['date_depart']) ? 'is-invalid' : '' ?>"
             value="<?= Security::h($old['date_depart'] ?? '') ?>">
      <?php if (isset($errors['date_depart'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['date_depart']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Heure -->
    <div class="col-md-6">
      <label for="heure_depart" class="form-label">Heure</label>
      <input type="time" name="heure_depart" id="heure_depart"
             class="form-control <?= isset($errors['heure_depart']) ? 'is-invalid' : '' ?>"
             value="<?= Security::h(substr($old['heure_depart'] ?? '', 0, 5)) ?>">
      <?php if (isset($errors['heure_depart'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['heure_depart']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Nombre de places -->
    <div class="col-md-4">
      <label for="nb_places" class="form-label">Places</label>
      <input type="number" name="nb_places" id="nb_places"
             class="form-control <?= isset($errors['nb_places']) ? 'is-invalid' : '' ?>"
             value="<?= Security::h($old['nb_places'] ?? '') ?>">
      <?php if (isset($errors['nb_places'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['nb_places']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Prix -->
    <div class="col-md-4">
      <label for="prix" class="form-label">Prix (€)</label>
      <input type="number" step="0.01" name="prix" id="prix"
             class="form-control <?= isset($errors['prix']) ? 'is-invalid' : '' ?>"
             value="<?= Security::h($old['prix'] ?? '') ?>">
      <?php if (isset($errors['prix'])): ?>
        <div class="invalid-feedback"><?= Security::h($errors['prix']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Description -->
    <div class="col-md-12">
      <label for="description" class="form-label">Description</label>
      <textarea name="description" id="description" rows="3"
                class="form-control"><?= Security::h($old['description'] ?? '') ?></textarea>
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-success">Enregistrer</button>
    </div>
  </form>
</div>
