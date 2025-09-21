<?php
/**
 * View: trajets/show.php
 * Données disponibles:
 * - $trajet: [
 *     'id_trajet', 'ville_depart', 'ville_arrivee',
 *     'date_depart', 'heure_depart',
 *     'nb_places', 'prix', 'description'
 *   ]
 */
use App\Core\Security;
?>
<div class="container my-4">
  <a href="/trajets" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour à la liste</a>

  <!-- Flash message -->
  <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-info text-center my-3">
          <?= htmlspecialchars($_SESSION['flash']) ?>
      </div>
      <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-header">
      <h2 class="h5 mb-0">
        Trajet #<?= (int)$trajet['id_trajet'] ?>
      </h2>
    </div>

    <div class="card-body">
      <div class="row g-3">
        <!-- Départ -->
        <div class="col-12 col-md-6">
          <div class="border rounded p-3">
            <div class="fw-semibold text-muted">Départ</div>
            <div class="fs-5"><?= Security::h($trajet['ville_depart']) ?></div>
          </div>
        </div>

        <!-- Arrivée -->
        <div class="col-12 col-md-6">
          <div class="border rounded p-3">
            <div class="fw-semibold text-muted">Arrivée</div>
            <div class="fs-5"><?= Security::h($trajet['ville_arrivee']) ?></div>
          </div>
        </div>

        <!-- Date -->
        <div class="col-6 col-md-3">
          <div class="border rounded p-3">
            <div class="fw-semibold text-muted">Date</div>
            <div class="fs-6"><?= (new DateTime($trajet['date_depart']))->format('d/m/Y') ?></div>
          </div>
        </div>

        <!-- Heure -->
        <div class="col-6 col-md-3">
          <div class="border rounded p-3">
            <div class="fw-semibold text-muted">Heure</div>
            <div><?= (new DateTime($trajet['heure_depart']))->format('H:i') ?></div>
          </div>
        </div>

        <!-- Places -->
        <div class="col-6 col-md-3">
          <div class="border rounded p-3">
            <div class="fw-semibold text-muted">Places</div>
            <div><?= (int)$trajet['nb_places'] ?></div>
          </div>
        </div>

        <!-- Prix -->
        <div class="col-6 col-md-3">
          <div class="border rounded p-3">
            <div class="fw-semibold text-muted">Prix</div>
            <div><?= number_format((float)$trajet['prix'], 2, ',', ' ') ?> €</div>
          </div>
        </div>

        <!-- Description -->
        <?php if (!empty($trajet['description'])): ?>
        <div class="col-12">
          <div class="border rounded p-3">
            <div class="fw-semibold text-muted">Description</div>
            <div><?= nl2br(Security::h($trajet['description'])) ?></div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="card-footer d-flex gap-2">
      <a href="/trajets/<?= (int)$trajet['id_trajet'] ?>/edit" class="btn btn-primary">Modifier</a>
      <form action="/trajets/<?= (int)$trajet['id_trajet'] ?>/delete" method="post" onsubmit="return confirm('Voulez-vous vraiment supprimer ce trajet ?');"
        class="d-inline">
        <?= Security::csrfField() ?>
        <button type="submit" class="btn btn-danger">Supprimer</button>
      </form>
    </div>

    <!-- Formulaire réservation -->
    <?php if (!empty($_SESSION['user'])): ?>
        <form method="post" action="/reservation/store" class="mt-3">
            <input type="hidden" name="id_trajet" value="<?= htmlspecialchars($trajet['id_trajet']) ?>">
            <button type="submit" class="btn btn-success w-100">
                Réserver ce trajet
            </button>
        </form>
    <?php else: ?>
        <div class="alert alert-info mt-3">
            <a href="/login">Connectez-vous</a> pour réserver ce trajet.
        </div>
    <?php endif; ?>
  </div>
</div>
