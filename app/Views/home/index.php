<?php
/**
 * View: home/index.php
 * Variables disponibles :
 * - $titre (string)
 * - $description (string)
 */
?>
<section class="container mt-5">
  <div class="row align-items-center">
    <div class="col-12 col-lg-7">
      <h1 class="mb-3"><?= $titre ?? 'Page d’accueil' ?></h1>
      <p class="lead"><?= $description ?? 'Bienvenue sur la plateforme EcoRide.' ?></p>
      <div class="d-flex flex-wrap gap-2 mt-4">
        <a href="/trajets" class="btn btn-primary">Voir les trajets</a>
        <a href="/trajets/create" class="btn btn-success">Proposer un trajet</a>
      </div>
    </div>
    <div class="col-12 col-lg-5 mt-4 mt-lg-0">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="mb-3">Rechercher un trajet</h5>
          <form method="get" action="/trajets" class="row g-3">
            <div class="col-md-6">
              <label for="ville_depart" class="form-label">Départ</label>
              <input type="text" name="ville_depart" id="ville_depart" class="form-control" placeholder="Ex: Paris">
            </div>
            <div class="col-md-6">
              <label for="ville_arrivee" class="form-label">Arrivée</label>
              <input type="text" name="ville_arrivee" id="ville_arrivee" class="form-control" placeholder="Ex: Lyon">
            </div>
            <div class="col-md-6">
              <label for="date_depart" class="form-label">Date</label>
              <input type="date" name="date_depart" id="date_depart" class="form-control">
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <button type="submit" class="btn btn-success w-100">Rechercher</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
