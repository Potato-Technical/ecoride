<?php
/**
 * View: trajets/index.php
 * Données disponibles :
 * - $trajets (array)
 */
use App\Core\Security;
?>
<div class="container my-4">
  <h1 class="mb-4">Liste des trajets</h1>
  <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-info text-center my-3">
          <?php echo htmlspecialchars($_SESSION['flash']); ?>
      </div>
      <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <a href="/trajets/create" class="btn btn-success mb-3">+ Proposer un trajet</a>

  <?php if (empty($trajets)): ?>
    <div class="alert alert-info">Aucun trajet disponible pour le moment.</div>
  <?php else: ?>
    <div class="table-responsive shadow-sm">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Départ</th>
            <th>Arrivée</th>
            <th>Date</th>
            <th>Heure</th>
            <th>Places</th>
            <th>Prix</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($trajets as $t): ?>
            <tr>
              <td><?= (int)$t['id_trajet'] ?></td>
              <td><?= Security::h($t['ville_depart']) ?></td>
              <td><?= Security::h($t['ville_arrivee']) ?></td>
              <td><?= (new DateTime($t['date_depart']))->format('d/m/Y') ?></td>
              <td><?= (new DateTime($t['heure_depart']))->format('H:i') ?></td>
              <td><?= (int)$t['nb_places'] ?></td>
              <td><?= number_format((float)$t['prix'], 2, ',', ' ') ?></td>
              <td>
                <a href="/trajets/<?= (int)$t['id_trajet'] ?>" class="btn btn-sm btn-outline-primary">Voir détail</a>

                <?php
                $isConducteur = isset($_SESSION['user']['id']) && $_SESSION['user']['id'] === $t['id_conducteur'];
                $isAdmin = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
                if ($isConducteur || $isAdmin): ?>
                    <a href="/trajets/<?= (int)$t['id_trajet'] ?>/edit" class="btn btn-sm btn-outline-warning">Modifier</a>
                    <form method="post" action="/trajets/<?= (int)$t['id_trajet'] ?>/delete"
                          onsubmit="return confirm('Voulez-vous vraiment supprimer ce trajet ?');"
                          class="d-inline">
                        <?= Security::csrfField() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                    </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
