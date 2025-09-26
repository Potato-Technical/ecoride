<?php
/**
 * View: trajets/index.php
 * Données disponibles :
 * - $trajets (array)
 * - $hasMore (bool) → défini dans le contrôleur si + de résultats existent
 */
use App\Core\Security;
?>
<div class="container my-4">
  <h1 class="mb-4">Liste des trajets</h1>

  <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-info text-center my-3">
          <?= htmlspecialchars($_SESSION['flash']) ?>
      </div>
      <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <a href="/trajets/create" class="btn btn-success mb-4">Proposer un trajet</a>

  <!-- Barre de recherche -->
  <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
    <form method="get" action="/trajets" class="row g-3 align-items-end">
      <div class="col-12 col-md-4">
        <label for="ville_depart" class="form-label">Ville de départ</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
          <input type="text" name="ville_depart" id="ville_depart" class="form-control"
                 placeholder="Ex: Paris" value="<?= htmlspecialchars($_GET['ville_depart'] ?? '') ?>">
        </div>
      </div>
      <div class="col-12 col-md-4">
        <label for="ville_arrivee" class="form-label">Ville d’arrivée</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-flag"></i></span>
          <input type="text" name="ville_arrivee" id="ville_arrivee" class="form-control"
                 placeholder="Ex: Lyon" value="<?= htmlspecialchars($_GET['ville_arrivee'] ?? '') ?>">
        </div>
      </div>
      <div class="col-12 col-md-3">
        <label for="date_depart" class="form-label">Date de départ</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
          <input type="date" name="date_depart" id="date_depart" class="form-control"
                 value="<?= htmlspecialchars($_GET['date_depart'] ?? '') ?>">
        </div>
      </div>
      <div class="col-12 col-md-1">
        <button type="submit" class="btn btn-success w-100">
          <i class="bi bi-search"></i>
        </button>
      </div>
    </form>
  </div>

  <!-- Bloc filtres / tri -->
  <div class="row mb-4">
    <div class="col-lg-3 mb-3 mb-lg-0">
      <div class="card border-0 shadow-sm rounded-4 p-3">
        <h6 class="fw-bold mb-3">Trier / Filtrer</h6>
        <form method="get" action="/trajets">
          <!-- On garde les valeurs de recherche déjà saisies -->
          <input type="hidden" name="ville_depart" value="<?= htmlspecialchars($_GET['ville_depart'] ?? '') ?>">
          <input type="hidden" name="ville_arrivee" value="<?= htmlspecialchars($_GET['ville_arrivee'] ?? '') ?>">
          <input type="hidden" name="date_depart" value="<?= htmlspecialchars($_GET['date_depart'] ?? '') ?>">

          <ul class="list-unstyled mb-0">
            <li class="mb-2">
              <input type="checkbox" name="filters[]" value="eco" id="filter-eco"
                <?= (isset($_GET['filters']) && in_array('eco', $_GET['filters'])) ? 'checked' : '' ?>>
              <label for="filter-eco">Voyage écologique</label>
            </li>
            <li class="mb-2">
              <input type="checkbox" name="filters[]" value="prix" id="filter-prix"
                <?= (isset($_GET['filters']) && in_array('prix', $_GET['filters'])) ? 'checked' : '' ?>>
              <label for="filter-prix">Prix le plus bas</label>
            </li>
            <li class="mb-2">
              <input type="checkbox" name="filters[]" value="duree" id="filter-duree"
                <?= (isset($_GET['filters']) && in_array('duree', $_GET['filters'])) ? 'checked' : '' ?>>
              <label for="filter-duree">Trajet le plus court</label>
            </li>
          </ul>

          <button type="submit" class="btn btn-sm btn-success w-100 mt-3">Appliquer</button>
          <a href="/trajets" class="btn btn-sm btn-outline-secondary w-100 mt-2">Tout effacer</a>
        </form>
      </div>
    </div>

    <!-- Liste des trajets -->
    <div class="col-lg-9">
      <?php if (empty($trajets)): ?>
        <div class="alert alert-info">Aucun trajet disponible pour le moment.</div>
      <?php else: ?>
        <div class="d-flex flex-column gap-3">
          <?php foreach ($trajets as $t): ?>
            <div class="card shadow-sm border-0 rounded-4 p-3">
              <div class="row align-items-center">
                <!-- Infos trajet -->
                <div class="col-md-4">
                  <div class="fw-bold"><?= Security::h($t['ville_depart']) ?> → <?= Security::h($t['ville_arrivee']) ?></div>
                  <div class="text-muted small">
                    <?= (new DateTime($t['date_depart']))->format('d/m/Y') ?>
                    à <?= (new DateTime($t['heure_depart']))->format('H:i') ?>
                  </div>
                </div>

                <!-- Places & prix -->
                <div class="col-md-3">
                  <div><?= (int)$t['nb_places'] ?> place(s)</div>
                  <div class="fw-bold text-success fs-5">
                    <?= number_format((float)$t['prix'], 2, ',', ' ') ?> €
                  </div>
                </div>

                <!-- Actions -->
                <div class="col-md-5 text-end">
                  <a href="/trajets/<?= (int)$t['id_trajet'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">Voir détail</a>
                  <?php
                  $isConducteur = isset($_SESSION['user']['id']) && $_SESSION['user']['id'] === $t['id_conducteur'];
                  $isAdmin = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
                  if ($isConducteur || $isAdmin): ?>
                      <a href="/trajets/<?= (int)$t['id_trajet'] ?>/edit" class="btn btn-sm btn-outline-warning rounded-pill">Modifier</a>
                      <form method="post" action="/trajets/<?= (int)$t['id_trajet'] ?>/delete"
                            onsubmit="return confirm('Voulez-vous vraiment supprimer ce trajet ?');"
                            class="d-inline">
                          <?= Security::csrfField() ?>
                          <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Supprimer</button>
                      </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Charger plus -->
        <?php if (!empty($hasMore) && $hasMore === true): ?>
          <div class="text-center mt-4">
            <button class="btn btn-success rounded-pill px-5">Charger plus de résultats</button>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
