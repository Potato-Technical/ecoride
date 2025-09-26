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
<div class="container my-5" style="max-width: 800px;">
  <!-- Barre de navigation locale -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="btn-group">
      <a href="/profil" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-person-circle"></i> Profil
      </a>
      <a href="/vehicules" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-car-front"></i> Mes véhicules
      </a>
    </div>
  </div>

  <!-- Flash -->
  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center rounded-4 shadow-sm mb-4">
      <?= Security::h($_SESSION['flash']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- Carte véhicule -->
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-light rounded-top-4">
      <h2 class="h5 mb-0">
        <i class="bi bi-car-front text-success"></i>
        Véhicule #<?= (int)$vehicule['id_vehicule'] ?>
      </h2>
    </div>

    <div class="card-body">
      <div class="row g-3">
        <!-- Marque -->
        <div class="col-12 col-md-6">
          <div class="border rounded-3 p-3 bg-white h-100">
            <div class="fw-semibold text-muted">Marque</div>
            <div class="fs-5"><?= Security::h($vehicule['marque']) ?></div>
          </div>
        </div>

        <!-- Modèle -->
        <div class="col-12 col-md-6">
          <div class="border rounded-3 p-3 bg-white h-100">
            <div class="fw-semibold text-muted">Modèle</div>
            <div class="fs-5"><?= Security::h($vehicule['modele']) ?></div>
          </div>
        </div>

        <!-- Immatriculation -->
        <div class="col-12 col-md-6">
          <div class="border rounded-3 p-3 bg-white h-100">
            <div class="fw-semibold text-muted">Immatriculation</div>
            <span class="badge bg-secondary fs-6 px-3 py-2">
              <?= Security::h($vehicule['immatriculation']) ?>
            </span>
          </div>
        </div>

        <!-- Places -->
        <div class="col-12 col-md-6">
          <div class="border rounded-3 p-3 bg-white h-100">
            <div class="fw-semibold text-muted">Places</div>
            <span class="badge bg-success fs-6 px-3 py-2">
              <?= (int)$vehicule['nb_places'] ?> places
            </span>
          </div>
        </div>

        <!-- Date ajout -->
        <div class="col-12">
          <div class="border rounded-3 p-3 bg-white">
            <div class="fw-semibold text-muted">Ajouté le</div>
            <div class="fs-6">
              <?= (new DateTime($vehicule['created_at']))->format('d/m/Y H:i') ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Actions -->
    <?php 
    $isOwner = isset($_SESSION['user']['id']) && $_SESSION['user']['id'] === $vehicule['proprietaire'];
    $isAdmin = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
    if ($isOwner || $isAdmin): ?>
      <div class="card-footer d-flex justify-content-end gap-2">
        <a href="/vehicules/<?= (int)$vehicule['id_vehicule'] ?>/edit" 
           class="btn btn-warning rounded-pill">
          <i class="bi bi-pencil"></i> Modifier
        </a>
        <form action="/vehicules/<?= (int)$vehicule['id_vehicule'] ?>/delete" method="post"
              onsubmit="return confirm('Voulez-vous vraiment supprimer ce véhicule ?');"
              class="d-inline">
          <?= Security::csrfField() ?>
          <button type="submit" class="btn btn-danger rounded-pill">
            <i class="bi bi-trash"></i> Supprimer
          </button>
        </form>
      </div>
    <?php endif; ?>
  </div>
</div>
