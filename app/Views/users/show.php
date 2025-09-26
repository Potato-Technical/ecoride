<?php
/**
 * View: users/show.php
 * Données disponibles:
 * - $user: [
 *     'id_user','nom','prenom','email','role','credits'
 *   ]
 */
use App\Core\Security;
?>
<div class="container my-5">
  <div class="row g-4">
    
    <!-- Colonne gauche : Profil utilisateur -->
    <div class="col-12 col-lg-4">
      <div class="card shadow-sm border-0 rounded-4 text-center p-4 h-100">
        <!-- Avatar rond avec initiales -->
        <div class="mb-3">
          <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center"
               style="width: 100px; height: 100px; font-size: 2rem; font-weight: bold;">
            <?= strtoupper(substr(Security::h($user['prenom'] ?? ''), 0, 1)) ?>
            <?= strtoupper(substr(Security::h($user['nom'] ?? ''), 0, 1)) ?>
          </div>
        </div>

        <!-- Nom complet -->
        <h4 class="fw-bold mb-1"><?= Security::h(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></h4>
        <!-- Email -->
        <p class="text-muted mb-2"><?= Security::h($user['email'] ?? '') ?></p>

        <!-- Rôle + Crédits -->
        <div class="d-flex flex-column gap-2 align-items-center mb-3">
          <span class="badge bg-success px-3 py-2 rounded-pill text-white">
            <?= ucfirst(Security::h($user['role'] ?? '')) ?>
          </span>
          <span class="badge-credits"><?= (int)($user['credits'] ?? 0) ?> crédits</span>
        </div>

        <!-- Boutons principaux -->
        <div class="d-flex flex-column gap-2">
          <a href="/profil/edit" class="btn btn-outline-primary rounded-pill">Modifier profil</a>
          <form method="post" action="/profil/delete"
                onsubmit="return confirm('Voulez-vous vraiment supprimer votre compte ?');" class="d-inline">
            <?= Security::csrfField() ?>
            <button type="submit" class="btn btn-outline-danger rounded-pill w-100">Supprimer compte</button>
          </form>
          <form method="post" action="/profil/switch-role" class="d-inline">
            <?= Security::csrfField() ?>
            <button type="submit" class="btn btn-warning rounded-pill w-100">
              <?= ($_SESSION['user']['role'] === 'passager') ? 'Devenir conducteur' : 'Revenir passager' ?>
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Colonne droite : Actions et infos -->
    <div class="col-12 col-lg-8 d-flex flex-column gap-4">

      <!-- Bloc trajets & crédits -->
      <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="fw-bold mb-3">Trajets & crédits</h5>
        <div class="d-flex flex-wrap gap-2">
          <a href="/mes-trajets" class="btn btn-outline-primary rounded-pill">Mes trajets</a>
          <a href="/trajets/create" class="btn btn-success rounded-pill">Proposer un trajet</a>
          <a href="/mes-reservations" class="btn btn-outline-info rounded-pill">Mes réservations</a>
          <form method="post" action="/profil/add-credits" class="d-inline">
            <?= Security::csrfField() ?>
            <button type="submit" class="btn btn-success rounded-pill">+10 crédits</button>
          </form>
        </div>
      </div>

      <!-- Bloc véhicules -->
      <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="fw-bold mb-3">Mes véhicules</h5>
        <div class="d-flex flex-wrap gap-2">
          <a href="/vehicules" class="btn btn-outline-success rounded-pill">Voir mes véhicules</a>
          <a href="/vehicules/nouveau" class="btn btn-success rounded-pill">Ajouter un véhicule</a>
        </div>
      </div>

      <!-- Bloc signalement -->
      <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="fw-bold mb-3">Signaler un incident</h5>
        <form method="post" action="/incidents/store">
          <?= Security::csrfField() ?>
          <textarea name="description" class="form-control mb-3 rounded-3"
                    placeholder="Décrivez l’incident..." required></textarea>
          <button type="submit" class="btn btn-warning rounded-pill">Envoyer</button>
        </form>
      </div>

    </div>
  </div>
</div>
