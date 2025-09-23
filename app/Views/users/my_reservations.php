<?php
/**
 * View: reservations/my_reservations.php
 * Données disponibles:
 * - $reservations: [
 *     'id_reservation','trajet_id','ville_depart','ville_arrivee',
 *     'date_depart','heure_depart','statut'
 *   ]
 */
use App\Core\Security;
?>
<div class="container my-4">
  <a href="/profil" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour au profil</a>

  <h1 class="mb-4">Mes réservations</h1>

  <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-info text-center my-3">
          <?= Security::h($_SESSION['flash']) ?>
      </div>
      <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <?php if (empty($reservations)): ?>
      <div class="alert alert-warning">Vous n’avez encore aucune réservation.</div>
  <?php else: ?>
      <div class="list-group shadow-sm">
        <?php foreach ($reservations as $res): ?>
          <div class="list-group-item">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
              <!-- Infos trajet -->
              <div class="me-3">
                <a href="/trajets/<?= (int)$res['trajet_id'] ?>" class="text-decoration-none">
                  <strong><?= Security::h($res['ville_depart']) ?></strong>
                  → <?= Security::h($res['ville_arrivee']) ?><br>
                  <small class="text-muted">
                    Le <?= (new DateTime($res['date_depart']))->format('d/m/Y') ?> 
                    à <?= substr($res['heure_depart'], 0, 5) ?>
                  </small>
                </a>
              </div>

              <!-- Statut + actions -->
              <div class="text-end flex-grow-1">
                <span class="badge bg-info mb-2"><?= Security::h($res['statut']) ?></span><br>

                <?php if ($res['statut'] === 'confirmée' && $_SESSION['user']['role'] === 'passager'): ?>
                  <!-- Annuler réservation -->
                  <form method="post" action="/reservation/<?= (int)$res['id_reservation'] ?>/cancel" 
                        class="d-inline"
                        onsubmit="return confirm('Annuler cette réservation ?');">
                    <?= Security::csrfField() ?>
                    <button type="submit" class="btn btn-sm btn-danger">Annuler</button>
                  </form>

                  <!-- Laisser un avis -->
                  <form method="post" action="/avis/store" class="mt-2">
                    <?= Security::csrfField() ?>
                    <textarea name="contenu" class="form-control form-control-sm mb-2" 
                              placeholder="Votre avis..." required></textarea>
                    <button type="submit" class="btn btn-sm btn-primary">Envoyer l’avis</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
  <?php endif; ?>
</div>
