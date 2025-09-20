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
?>
<div class="container my-4">
  <a href="/trajets" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour à la liste</a>

  <div class="card shadow-sm">
    <div class="card-header">
      <h2 class="h5 mb-0">
        Trajet #<?= htmlspecialchars((string)$trajet['id_trajet']) ?>
      </h2>
    </div>

    <div class="card-body">
      <div class="row g-3">
        <!-- Départ -->
        <div class="col-12 col-md-6">
          <div class="border rounded p-3">
            <div class="fw-semibold text-muted">Départ</div>
            <div class="fs-5"><?= htmlspecialchars((string)$trajet['ville_depart']) ?></div>
          </div>
        </div>

        <!-- Arrivée -->
        <div class="col-12 col-md-6">
          <div class="border rounded p-3">
            <div class="fw-semibold text-muted">Arrivée</div>
            <div class="fs-5"><?= htmlspecialchars((string)$trajet['ville_arrivee']) ?></div>
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
            <div><?= nl2br(htmlspecialchars_decode((string)$trajet['description'])) ?></div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="card-footer d-flex gap-2">
      <a href="/trajets/<?= (int)$trajet['id_trajet'] ?>/edit" class="btn btn-primary">Modifier</a>
      <form action="/trajets/<?= (int)$trajet['id_trajet'] ?>/delete" method="post" onsubmit="return confirm('Supprimer ce trajet ?');">
        <button type="submit" class="btn btn-danger">Supprimer</button>
      </form>
    </div>
  </div>
</div>
