<?php
use App\Core\Security;
?>
<div class="container my-4">
  <h1>Mon profil</h1>

  <!-- Message flash -->
  <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-info text-center my-3">
          <?= Security::h($_SESSION['flash']) ?>
      </div>
      <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <!-- Infos utilisateur -->
      <p><strong>Nom :</strong> <?= Security::h($user['nom'] ?? '') ?></p>
      <p><strong>Prénom :</strong> <?= Security::h($user['prenom'] ?? '') ?></p>
      <p><strong>Email :</strong> <?= Security::h($user['email'] ?? '') ?></p>
      <p><strong>Rôle :</strong> <?= Security::h($user['role'] ?? '') ?></p>
      <p><strong>Crédits :</strong> <?= (int)($user['credits'] ?? 0) ?></p>
    </div>

    <div class="card-footer d-flex flex-wrap gap-2">
      <!-- Gestion profil (toujours dispo) -->
      <a href="/profil/edit" class="btn btn-primary">Modifier profil</a>
      <form method="post" action="/profil/delete" 
            onsubmit="return confirm('Voulez-vous vraiment supprimer votre compte ?');">
        <?= Security::csrfField() ?>
        <button type="submit" class="btn btn-danger">Supprimer compte</button>
      </form>

      <?php if ($_SESSION['user']['role'] === 'passager' || $_SESSION['user']['role'] === 'conducteur'): ?>
        <!-- Gestion trajets -->
        <a href="/mes-trajets" class="btn btn-outline-primary">Mes trajets</a>
        <a href="/trajets/create" class="btn btn-success">Proposer un trajet</a>

        <!-- Gestion réservations -->
        <a href="/mes-reservations" class="btn btn-outline-info">Mes réservations</a>

        <!-- Bloc crédits -->
        <form method="post" action="/profil/add-credits" class="d-inline">
          <?= Security::csrfField() ?>
          <button type="submit" class="btn btn-success">+10 crédits</button>
        </form>

        <!-- Gestion véhicules -->
        <a href="/vehicules" class="btn btn-outline-success">Mes véhicules</a>
        <a href="/vehicules/nouveau" class="btn btn-success">Ajouter un véhicule</a>

        <!-- Switch rôle passager <-> conducteur -->
        <form method="post" action="/profil/switch-role" class="d-inline">
          <?= Security::csrfField() ?>
          <button type="submit" class="btn btn-warning">
            <?= ($_SESSION['user']['role'] === 'passager') ? 'Devenir conducteur' : 'Revenir passager' ?>
          </button>
        </form>

        <!-- Bloc signalement incident -->
        <div class="card shadow-sm mt-4">
          <div class="card-body">
            <h5>Signaler un incident</h5>
            <form method="post" action="/incidents/store">
              <?= Security::csrfField() ?>
              <textarea name="description" class="form-control mb-2" placeholder="Décrivez l’incident..." required></textarea>
              <button type="submit" class="btn btn-sm btn-warning">Signaler</button>
            </form>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
