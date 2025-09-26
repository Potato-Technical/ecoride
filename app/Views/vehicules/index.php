<?php
/**
 * View: vehicules/index.php
 * Données disponibles :
 * - $vehicules (array)
 */
use App\Core\Security;
?>
<div class="container my-5">
  <!-- Retour -->
  <a href="/profil" class="btn btn-outline-secondary btn-sm mb-4">
    <i class="bi bi-person-circle"></i> Retour au profil
  </a>

  <!-- Titre -->
  <h1 class="fw-bold mb-4 text-center">
    <i class="bi bi-car-front text-success"></i> Mes véhicules
  </h1>

  <!-- Flash message -->
  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-info text-center rounded-4 shadow-sm">
      <?= Security::h($_SESSION['flash']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- Ajouter véhicule -->
  <div class="text-center mb-4">
    <a href="/vehicules/nouveau" class="btn btn-success rounded-pill px-4">
      <i class="bi bi-plus-circle"></i> Ajouter un véhicule
    </a>
  </div>

  <!-- Liste des véhicules -->
  <?php if (empty($vehicules)): ?>
    <div class="alert alert-warning text-center rounded-4 shadow-sm py-4">
      <i class="bi bi-exclamation-triangle me-2"></i>
      Vous n’avez enregistré aucun véhicule pour le moment.
    </div>
  <?php else: ?>
    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table align-middle mb-0 table-hover">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Marque</th>
                <th>Modèle</th>
                <th>Immatriculation</th>
                <th class="text-center">Places</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($vehicules as $v): ?>
                <tr>
                  <!-- ID -->
                  <td class="fw-bold text-muted"><?= (int)$v['id_vehicule'] ?></td>
                  <!-- Marque -->
                  <td><?= Security::h($v['marque']) ?></td>
                  <!-- Modèle -->
                  <td><?= Security::h($v['modele']) ?></td>
                  <!-- Immatriculation -->
                  <td>
                    <span class="badge bg-secondary px-3 py-2">
                      <?= Security::h($v['immatriculation']) ?>
                    </span>
                  </td>
                  <!-- Places -->
                  <td class="text-center">
                    <span class="badge bg-success px-3 py-2">
                      <?= (int)$v['nb_places'] ?> places
                    </span>
                  </td>
                  <!-- Actions -->
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      <a href="/vehicules/<?= (int)$v['id_vehicule'] ?>" 
                         class="btn btn-sm btn-outline-primary rounded-pill">
                        <i class="bi bi-eye"></i> Voir
                      </a>
                      <a href="/vehicules/<?= (int)$v['id_vehicule'] ?>/edit" 
                         class="btn btn-sm btn-outline-warning rounded-pill">
                        <i class="bi bi-pencil"></i> Modifier
                      </a>
                      <form action="/vehicules/<?= (int)$v['id_vehicule'] ?>/delete" 
                            method="post"
                            onsubmit="return confirm('Voulez-vous vraiment supprimer ce véhicule ?');"
                            class="d-inline">
                        <?= Security::csrfField() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                          <i class="bi bi-trash"></i> Supprimer
                        </button>
                      </form>
                    </div>
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
