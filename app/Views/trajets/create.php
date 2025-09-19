<?php
// Variables attendues: $old (array), $errors (array)
$old    = $old    ?? [];
$errors = $errors ?? [];
?>
<div class="container py-3">
  <a class="btn btn-outline-secondary btn-sm mb-3" href="/trajets">&larr; Retour</a>
  <h1 class="h4 mb-3">Créer un trajet</h1>

  <form method="post" action="/trajets/store" class="row g-3">
    <div class="col-12 col-md-6">
      <label class="form-label">Départ*</label>
      <input
        name="depart"
        class="form-control <?= isset($errors['depart']) ? 'is-invalid' : '' ?>"
        value="<?= htmlspecialchars($old['depart'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <?php if (isset($errors['depart'])): ?>
        <div class="invalid-feedback"><?= $errors['depart'] ?></div>
      <?php endif; ?>
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label">Arrivée*</label>
      <input
        name="arrivee"
        class="form-control <?= isset($errors['arrivee']) ? 'is-invalid' : '' ?>"
        value="<?= htmlspecialchars($old['arrivee'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <?php if (isset($errors['arrivee'])): ?>
        <div class="invalid-feedback"><?= $errors['arrivee'] ?></div>
      <?php endif; ?>
    </div>

    <div class="col-6 col-md-3">
      <label class="form-label">Date*</label>
      <input
        type="date"
        name="date_depart"
        class="form-control <?= isset($errors['date_depart']) ? 'is-invalid' : '' ?>"
        value="<?= htmlspecialchars($old['date_depart'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <?php if (isset($errors['date_depart'])): ?>
        <div class="invalid-feedback"><?= $errors['date_depart'] ?></div>
      <?php endif; ?>
    </div>

    <div class="col-6 col-md-3">
      <label class="form-label">Heure*</label>
      <input
        type="time"
        name="heure_depart"
        class="form-control <?= isset($errors['heure_depart']) ? 'is-invalid' : '' ?>"
        value="<?= htmlspecialchars($old['heure_depart'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <?php if (isset($errors['heure_depart'])): ?>
        <div class="invalid-feedback"><?= $errors['heure_depart'] ?></div>
      <?php endif; ?>
    </div>

    <div class="col-6 col-md-3">
      <label class="form-label">Places*</label>
      <input
        type="number" min="1" step="1"
        name="places"
        class="form-control <?= isset($errors['places']) ? 'is-invalid' : '' ?>"
        value="<?= htmlspecialchars($old['places'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <?php if (isset($errors['places'])): ?>
        <div class="invalid-feedback"><?= $errors['places'] ?></div>
      <?php endif; ?>
    </div>

    <div class="col-6 col-md-3">
      <label class="form-label">Prix (€)*</label>
      <input
        type="number" min="0" step="0.01"
        name="prix"
        class="form-control <?= isset($errors['prix']) ? 'is-invalid' : '' ?>"
        value="<?= htmlspecialchars($old['prix'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <?php if (isset($errors['prix'])): ?>
        <div class="invalid-feedback"><?= $errors['prix'] ?></div>
      <?php endif; ?>
    </div>

    <div class="col-12">
      <button class="btn btn-success" type="submit">Enregistrer</button>
    </div>
  </form>
</div>
