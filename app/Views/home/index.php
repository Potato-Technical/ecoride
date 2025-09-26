<?php
/**
 * View: home/index.php
 * Variables disponibles :
 * - $titre (string)
 * - $description (string)
 *
 * Cette vue correspond à la page d’accueil d’EcoRide.
 * Elle contient quatre grands blocs :
 *   1. Hero principal avec slogan, description et call-to-action.
 *   2. Formulaire de recherche d’un trajet.
 *   3. Section secondaire présentant les avantages du service.
 *   4. Appel à l’action final (inscription).
 */
?>

<!-- Bloc 1 : Section Hero -->
<section class="hero bg-light py-5" style="center/cover no-repeat;">
  <div class="container">
    <div class="row align-items-center">
      <!-- Partie gauche : texte d’accroche -->
      <div class="col-12 col-lg-6 text-center text-lg-start text-white">
        <!-- Titre principal -->
        <h1 class="display-3 fw-bold mb-4">
          <?= $titre ?? 'Voyagez autrement, ensemble' ?>
        </h1>

        <!-- Texte descriptif -->
        <p class="lead mb-5">
          <?= $description ?? 'EcoRide est la plateforme de covoiturage éco-responsable, simple et sécurisée. 
          Rejoignez une communauté engagée pour des trajets partagés, moins chers et plus verts.' ?>
        </p>

        <!-- Boutons Call-to-Action -->
        <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
          <a href="/trajets" class="btn btn-primary btn-lg px-5 py-3 rounded-pill">
            Voir les trajets disponibles
          </a>
          <a href="/trajets/create" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill">
            Proposer un trajet
          </a>
        </div>
      </div>

      <!-- Partie droite : image illustrative -->
      <!-- Affichée seulement sur desktop (lg et +) -->
      <div class="col-12 col-lg-6 text-center mt-5 mt-lg-0 d-none d-lg-block">
        <img src="/assets/img/illustration_covoiturage.png" alt="Illustration EcoRide" class="img-fluid rounded shadow-lg">
      </div>
    </div>
  </div>
</section>

<!-- Bloc 2 : Formulaire de recherche -->
<section class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-5">
          <!-- Titre du formulaire -->
          <h2 class="fw-bold text-center mb-4">Rechercher un trajet</h2>

          <!-- Formulaire -->
          <form method="get" action="/trajets" class="row g-4">
            <!-- Ville de départ -->
            <div class="col-12 col-md-4">
              <label for="ville_depart" class="form-label">Ville de départ</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                <input type="text" name="ville_depart" id="ville_depart" class="form-control" placeholder="Ex: Paris">
              </div>
            </div>

            <!-- Ville d’arrivée -->
            <div class="col-12 col-md-4">
              <label for="ville_arrivee" class="form-label">Ville d’arrivée</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-flag"></i></span>
                <input type="text" name="ville_arrivee" id="ville_arrivee" class="form-control" placeholder="Ex: Lyon">
              </div>
            </div>

            <!-- Date de départ + bouton recherche dans le même input-group -->
            <div class="col-12 col-md-4">
              <label for="date_depart" class="form-label">Date de départ</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input type="date" name="date_depart" id="date_depart" class="form-control">
                <button type="submit" class="btn btn-success px-4" aria-label="Rechercher">
                  <i class="bi bi-search"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- Bloc 3 : Section Avantages -->
<section class="container my-5 text-center">
  <h2 class="fw-bold mb-5">Pourquoi choisir EcoRide ?</h2>
  <div class="row g-5">
    <!-- Avantage 1 -->
    <div class="col-12 col-md-4">
      <i class="bi bi-tree text-success display-3"></i>
      <h5 class="mt-4">Écologique</h5>
      <p class="text-muted">
        Chaque trajet partagé permet de réduire les émissions de CO₂. 
        Avec EcoRide, contribuez activement à la préservation de notre planète.
      </p>
    </div>

    <!-- Avantage 2 -->
    <div class="col-12 col-md-4">
      <i class="bi bi-lightning-charge text-success display-3"></i>
      <h5 class="mt-4">Simple</h5>
      <p class="text-muted">
        Recherchez, proposez ou réservez un trajet en quelques clics. 
        Notre interface est conçue pour être claire et intuitive, accessible à tous.
      </p>
    </div>

    <!-- Avantage 3 -->
    <div class="col-12 col-md-4">
      <i class="bi bi-shield-check text-success display-3"></i>
      <h5 class="mt-4">Fiable</h5>
      <p class="text-muted">
        Profitez d’un système de crédits sécurisé, d’avis vérifiés et d’une communauté de confiance.
        EcoRide garantit un service propre et transparent.
      </p>
    </div>
  </div>
</section>

<!-- Bloc 4 : Appel à l’action final -->
<section class="bg-light py-5">
  <div class="container text-center">
    <h2 class="fw-bold mb-4">Prêt à rejoindre la communauté EcoRide ?</h2>
    <p class="lead mb-5">
      Créez un compte gratuitement, obtenez 20 crédits offerts et commencez à voyager autrement, dès aujourd’hui.
    </p>
    <a href="/register" class="btn btn-primary btn-lg px-5 py-3 rounded-pill">
      S’inscrire maintenant
    </a>
  </div>
</section>
