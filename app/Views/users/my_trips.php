<?php
/**
 * View: users/my_trips.php
 * Données disponibles:
 * - $trajets: [
 *     'id_trajet','ville_depart','ville_arrivee',
 *     'date_depart','id_reservation','prenom','nom','reservation_statut'
 *   ]
 */
use App\Core\Security;
?>
<div class="container my-4">
  <a href="/profil" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour au profil</a>

  <h1 class="mb-4">Mes trajets</h1>

  <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-info text-center my-3">
          <?= htmlspecialchars($_SESSION['flash']) ?>
      </div>
      <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <?php if (empty($trajets)): ?>
      <div class="alert alert-warning">Vous n’avez encore proposé aucun trajet.</div>
  <?php else: ?>
      <div class="list-group shadow-sm">
        <?php foreach ($trajets as $trajet): ?>
          <div class="list-group-item">
            <a href="/trajets/<?= (int)$trajet['id_trajet'] ?>" class="text-decoration-none">
              <strong><?= Security::h($trajet['ville_depart']) ?></strong>
              → <?= Security::h($trajet['ville_arrivee']) ?>
              (<?= (new DateTime($trajet['date_depart']))->format('d/m/Y') ?>)
            </a>

            <?php if (!empty($trajet['id_reservation'])): ?>
              <div class="mt-2 ms-3">
                Passager : <?= Security::h($trajet['prenom']) ?> <?= Security::h($trajet['nom']) ?>
                <span class="badge bg-info"><?= Security::h($trajet['reservation_statut']) ?></span>

                <?php if ($trajet['reservation_statut'] === 'confirmée'): ?>
                  <form method="post" action="/reservation/<?= (int)$trajet['id_reservation'] ?>/valider" class="d-inline">
                    <?= Security::csrfField() ?>
                    <button type="submit" class="btn btn-sm btn-success">Valider</button>
                  </form>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
  <?php endif; ?>
</div>
