<?php
use App\Core\Security;
?>
<div class="container my-4">
  <a href="/profil" class="btn btn-outline-secondary btn-sm mb-3">&larr; Retour au profil</a>

  <h1>Mes véhicules</h1>

  <!-- Message flash -->
  <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-info text-center my-3">
          <?= htmlspecialchars($_SESSION['flash']) ?>
      </div>
      <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <?php if (empty($vehicules)): ?>
      <!-- Aucun véhicule -->
      <div class="alert alert-warning">
        Vous n’avez encore enregistré aucun véhicule.
      </div>
      <a href="/vehicules/nouveau" class="btn btn-success">Ajouter un véhicule</a>
  <?php else: ?>
      <!-- Liste des véhicules -->
      <div class="list-group">
        <?php foreach ($vehicules as $v): ?>
          <a href="/vehicules/<?= (int)$v['id_vehicule'] ?>" 
             class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <div>
              <strong><?= Security::h($v['marque']) ?> <?= Security::h($v['modele']) ?></strong>
              <br>
              <small>Immatriculation : <?= Security::h($v['immatriculation']) ?></small>
              <br>
              <small>Places : <?= (int)$v['nb_places'] ?></small>
            </div>
            <span class="badge bg-secondary">
              Créé le <?= (new DateTime($v['created_at']))->format('d/m/Y') ?>
            </span>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- Bouton ajout -->
      <div class="mt-3">
        <a href="/vehicules/nouveau" class="btn btn-success">Ajouter un véhicule</a>
      </div>
  <?php endif; ?>
</div>
