<?php
/** View: trajets/index.php */
?>
<div class="container my-4">
  <h1 class="mb-4">Liste des trajets</h1>

  <a href="/trajets/create" class="btn btn-success mb-3">+ Proposer un trajet</a>

  <?php if (empty($trajets)): ?>
    <div class="alert alert-info">Aucun trajet disponible pour le moment.</div>
  <?php else: ?>
    <div class="table-responsive">
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
              <td><?= htmlspecialchars((string)$t['ville_depart']) ?></td>
              <td><?= htmlspecialchars((string)$t['ville_arrivee']) ?></td>
              <td><?= htmlspecialchars((string)$t['date_depart']) ?></td>
              <td><?= htmlspecialchars((string)$t['heure_depart']) ?></td>
              <td><?= (int)$t['nb_places'] ?></td>
              <td><?= number_format((float)$t['prix'], 2, ',', ' ') ?> €</td>
              <td>
                <!-- 👇 Ajout du lien Voir détail -->
                <a href="/trajets/<?= (int)$t['id_trajet'] ?>" class="btn btn-sm btn-outline-primary">Voir détail</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
