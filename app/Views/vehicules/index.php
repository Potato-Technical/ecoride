<?php
/**
 * View: vehicules/index.php
 * Données disponibles :
 * - $vehicules (array)
 */
use App\Core\Security;
?>
<div class="container my-4">
  <a href="/profil" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour au profil</a>

  <h1 class="mb-4">Mes véhicules</h1>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center my-3">
      <?= htmlspecialchars($_SESSION['flash']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <a href="/vehicules/nouveau" class="btn btn-success mb-3">+ Ajouter un véhicule</a>

  <?php if (empty($vehicules)): ?>
    <div class="alert alert-warning">Vous n’avez enregistré aucun véhicule pour le moment.</div>
  <?php else: ?>
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Marque</th>
                <th>Modèle</th>
                <th>Immatriculation</th>
                <th>Places</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($vehicules as $v): ?>
                <tr>
                  <td><?= (int)$v['id_vehicule'] ?></td>
                  <td><?= Security::h($v['marque']) ?></td>
                  <td><?= Security::h($v['modele']) ?></td>
                  <td><?= Security::h($v['immatriculation']) ?></td>
                  <td><?= (int)$v['nb_places'] ?></td>
                  <td>
                    <a href="/vehicules/<?= (int)$v['id_vehicule'] ?>" class="btn btn-sm btn-outline-primary">Voir</a>
                    <a href="/vehicules/<?= (int)$v['id_vehicule'] ?>/edit" class="btn btn-sm btn-outline-warning">Modifier</a>
                    <form action="/vehicules/<?= (int)$v['id_vehicule'] ?>/delete" method="post"
                          onsubmit="return confirm('Voulez-vous vraiment supprimer ce véhicule ?');"
                          class="d-inline">
                      <?= Security::csrfField() ?>
                      <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>
