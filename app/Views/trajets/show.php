<?php
/**
 * View: trajets/show.php
 * Données disponibles:
 * - $trajet: [
 *     'id_trajet', 'ville_depart', 'ville_arrivee',
 *     'date_depart', 'heure_depart',
 *     'nb_places', 'prix', 'description', 'id_conducteur'
 *   ]
 */
use App\Core\Security;
?>
<div class="container my-5">
  <!-- Retour -->
  <a href="/trajets" class="btn btn-outline-secondary btn-sm mb-4">&larr; Retour à la liste</a>

  <!-- Flash message -->
  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center rounded-pill">
      <?= Security::h($_SESSION['flash']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- Carte trajet -->
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-light">
      <h2 class="h5 fw-bold mb-0">
        <i class="bi bi-geo-alt text-success"></i>
        Trajet #<?= (int)$trajet['id_trajet'] ?>
      </h2>
    </div>

    <div class="card-body">
      <div class="row g-3 text-center text-md-start">
        <!-- Départ -->
        <div class="col-12 col-md-6">
          <div class="p-3 border rounded bg-white h-100">
            <div class="fw-semibold text-muted">Départ</div>
            <div class="fs-5 fw-bold text-dark">
              <?= Security::h($trajet['ville_depart']) ?>
            </div>
          </div>
        </div>

        <!-- Arrivée -->
        <div class="col-12 col-md-6">
          <div class="p-3 border rounded bg-white h-100">
            <div class="fw-semibold text-muted">Arrivée</div>
            <div class="fs-5 fw-bold text-dark">
              <?= Security::h($trajet['ville_arrivee']) ?>
            </div>
          </div>
        </div>

        <!-- Date -->
        <div class="col-6 col-md-3">
          <div class="p-3 border rounded bg-white">
            <div class="fw-semibold text-muted">Date</div>
            <div class="fs-6">
              <i class="bi bi-calendar-event text-success me-1"></i>
              <?= (new DateTime($trajet['date_depart']))->format('d/m/Y') ?>
            </div>
          </div>
        </div>

        <!-- Heure -->
        <div class="col-6 col-md-3">
          <div class="p-3 border rounded bg-white">
            <div class="fw-semibold text-muted">Heure</div>
            <div>
              <i class="bi bi-clock text-success me-1"></i>
              <?= (new DateTime($trajet['heure_depart']))->format('H:i') ?>
            </div>
          </div>
        </div>

        <!-- Places -->
        <div class="col-6 col-md-3">
          <div class="p-3 border rounded bg-white">
            <div class="fw-semibold text-muted">Places</div>
            <span class="badge bg-info px-3 py-2">
              <?= (int)$trajet['nb_places'] ?> disponibles
            </span>
          </div>
        </div>

        <!-- Prix -->
        <div class="col-6 col-md-3">
          <div class="p-3 border rounded bg-white">
            <div class="fw-semibold text-muted">Prix</div>
            <span class="fw-bold text-success fs-5">
              <?= number_format((float)$trajet['prix'], 2, ',', ' ') ?> €
            </span>
          </div>
        </div>

        <!-- Description -->
        <?php if (!empty($trajet['description'])): ?>
          <div class="col-12">
            <div class="p-3 border rounded bg-white">
              <div class="fw-semibold text-muted">Description</div>
              <p class="mb-0"><?= nl2br(Security::h($trajet['description'])) ?></p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Actions : Modifier / Supprimer -->
    <?php 
    $isConducteur = isset($_SESSION['user']['id']) && $_SESSION['user']['id'] === $trajet['id_conducteur'];
    $isAdmin = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
    if ($isConducteur || $isAdmin): ?>
      <div class="card-footer d-flex flex-wrap gap-2">
        <a href="/trajets/<?= (int)$trajet['id_trajet'] ?>/edit" class="btn btn-outline-primary rounded-pill">
          <i class="bi bi-pencil-square"></i> Modifier
        </a>
        <form action="/trajets/<?= (int)$trajet['id_trajet'] ?>/delete" method="post"
              onsubmit="return confirm('Voulez-vous vraiment supprimer ce trajet ?');"
              class="d-inline">
          <?= Security::csrfField() ?>
          <button type="submit" class="btn btn-outline-danger rounded-pill">
            <i class="bi bi-trash"></i> Supprimer
          </button>
        </form>
      </div>
    <?php endif; ?>

    <!-- Formulaire réservation -->
    <div class="card-footer">
      <?php if (!empty($_SESSION['user'])): ?>
          <form method="post" action="/reservation/store" class="d-flex flex-column flex-md-row gap-2">
              <?= Security::csrfField() ?>
              <input type="hidden" name="id_trajet" value="<?= Security::h($trajet['id_trajet']) ?>">
              <button type="submit" class="btn btn-success w-100 rounded-pill">
                <i class="bi bi-check2-circle"></i> Réserver ce trajet
              </button>
          </form>
      <?php else: ?>
          <div class="alert alert-info text-center mb-0 rounded-4">
            <i class="bi bi-info-circle me-1"></i>
            <a href="/login" class="fw-bold text-decoration-none">Connectez-vous</a> pour réserver ce trajet.
          </div>
      <?php endif; ?>
    </div>
  </div>
</div>
