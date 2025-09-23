<?php
/**
 * View: vehicules/show.php
 * Données disponibles:
 * - $vehicule: [
 *     'id_vehicule', 'marque', 'modele',
 *     'immatriculation', 'nb_places', 'proprietaire', 'created_at'
 *   ]
 */
use App\Core\Security;
?>
<div class="container my-4">
  <a href="/profil" class="btn btn-outline-secondary btn-sm mb-2">&larr; Retour au profil</a>
  <a href="/vehicules" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour à la liste</a>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center my-3">
      <?= htmlspecialchars($_SESSION['flash']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-header">
      <h2 class="h5 mb-0">Véhicule #<?= (int)$vehicule['id_vehicule'] ?></h2>
    </div>

    <div class="card-body">
      <div class="row g-3">
        <!-- Marque -->
        <div class="col-12 col-md-6">
          <div class="border rounded p-3">
            <div class="fw-semibold text-muted">Marque</div>
            <div class="fs-5"><?= Security::h($vehicule['marque']) ?></div>
          </div>
        </div>

        <!-- Modèle -->
        <div class="col-12 col-md-6">
          <div class="border rounded p-3">
            <div class="fw-semibold text-muted">Modèle</div>
            <div class="fs-5"><?= Security::h($vehicule['modele']) ?></div>
          </div>
        </div>

        <!-- Immatriculation -->
        <div class="col-12 col-md-6">
          <div class="border rounded p-3">
            <div class="fw-semibold text-muted">Immatriculation</div>
            <div class="fs-5"><?= Security::h($vehicule['immatriculation']) ?></div>
          </div>
        </div>

        <!-- Nombre de places -->
        <div class="col-12 col-md-6">
          <div class="border rounded p-3">
            <div class="fw-semibold text-muted">Places</div>
            <div class="fs-5"><?= (int)$vehicule['nb_places'] ?></div>
          </div>
        </div>

        <!-- Date d’ajout -->
        <div class="col-12">
          <div class="border rounded p-3">
            <div class="fw-semibold text-muted">Ajouté le</div>
            <div class="fs-6">
              <?= (new DateTime($vehicule['created_at']))->format('d/m/Y H:i') ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php 
    $isOwner = isset($_SESSION['user']['id']) && $_SESSION['user']['id'] === $vehicule['proprietaire'];
    $isAdmin = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
    if ($isOwner || $isAdmin): ?>
      <div class="card-footer d-flex gap-2">
        <a href="/vehicules/<?= (int)$vehicule['id_vehicule'] ?>/edit" class="btn btn-primary">Modifier</a>
        <form action="/vehicules/<?= (int)$vehicule['id_vehicule'] ?>/delete" method="post"
              onsubmit="return confirm('Voulez-vous vraiment supprimer ce véhicule ?');"
              class="d-inline">
          <?= Security::csrfField() ?>
          <button type="submit" class="btn btn-danger">Supprimer</button>
        </form>
      </div>
    <?php endif; ?>
  </div>
</div>
