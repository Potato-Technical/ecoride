<!-- HERO -->
<section class="home-hero py-5">
    <div class="container text-center">

        <h1 class="fw-semibold mb-3">EcoRide</h1>
        <p class="lead text-muted mb-4">
            Covoiturage simple, économique et plus responsable
        </p>

        <!-- BARRE DE RECHERCHE (UI ONLY) -->
        <div class="card shadow-sm p-3 mb-5 home-search-card">
            <div class="row g-2 align-items-center">

                <div class="col-12 col-md">
                    <input
                        type="text"
                        class="form-control"
                        placeholder="Lieu de départ">
                </div>

                <div class="col-12 col-md">
                    <input
                        type="text"
                        class="form-control"
                        placeholder="Destination">
                </div>

                <div class="col-6 col-md">
                    <input
                        type="date"
                        class="form-control">
                </div>

                <div class="col-6 col-md">
                    <input
                        type="number"
                        class="form-control"
                        placeholder="Passagers"
                        min="1">
                </div>

                <div class="col-12 col-md-auto">
                    <a href="/trajets"
                       class="btn btn-success px-4">
                        Voir les trajets
                    </a>
                </div>

            </div>
        </div>

    </div>
</section>


<!-- VALEURS -->
<section class="py-5">
    <div class="container">
        <div class="row text-center">

            <div class="col-md-4 mb-4">
                <h3 class="h6 fw-semibold mb-2">
                    Vos trajets préférés à petits prix
                </h3>
                <p class="text-muted">
                    Trouvez le trajet idéal parmi un large choix
                    de destinations accessibles.
                </p>
            </div>

            <div class="col-md-4 mb-4">
                <h3 class="h6 fw-semibold mb-2">
                    Voyagez en toute confiance
                </h3>
                <p class="text-muted">
                    Profils vérifiés, avis transparents et trajets fiables
                    pour voyager sereinement.
                </p>
            </div>

            <div class="col-md-4 mb-4">
                <h3 class="h6 fw-semibold mb-2">
                    Une mobilité plus responsable
                </h3>
                <p class="text-muted">
                    Réduisez votre impact environnemental
                    en partageant vos trajets.
                </p>
            </div>

        </div>
    </div>
</section>


<!-- POURQUOI ECORIDE -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-md-6 mb-4 mb-md-0">
                <picture>
                    <source srcset="/assets/images/home/80543.webp" type="image/webp">
                    <img
                        src="/assets/images/home/plateforme.webp"
                        alt="Pourquoi choisir EcoRide"
                        class="img-fluid rounded shadow-sm home-image">
                </picture>
            </div>

            <div class="col-md-6">
                <h2 class="h4 fw-semibold mb-3">
                    Pourquoi choisir EcoRide ?
                </h2>
                <p class="text-muted mb-4">
                    Chaque trajet est une opportunité d’agir pour la planète.
                    EcoRide favorise une mobilité économique,
                    responsable et conviviale.
                </p>

                <div class="d-flex justify-content-center">
                    <a href="/a-propos"
                    class="btn btn-outline-success">
                        Découvrir
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- PLATEFORME POUR TOUS -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-md-6 text-center text-md-start">
                <h2 class="h4 fw-semibold mb-3">
                    Une plateforme pensée pour tous
                </h2>

                <p class="text-muted mb-4">
                    EcoRide met en relation conducteurs et passagers
                    autour de trajets clairs, vérifiés et accessibles.
                    La simplicité et la transparence sont au cœur de la plateforme.
                </p>
            </div>

            <div class="col-md-6 mt-4 mt-md-0">
                <picture>
                    <source srcset="/assets/images/home/80541.webp" type="image/webp">
                    <img
                        src="/assets/images/home/communauté.webp"
                        alt="Communauté EcoRide"
                        class="img-fluid rounded shadow-sm home-image">
                </picture>
            </div>

        </div>
    </div>
</section>


<!-- CTA CONDUCTEUR -->
<section class="py-5 text-center">
    <div class="container">

        <h2 class="h4 fw-semibold mb-3">
            Envie de rejoindre EcoRide en tant que conducteur ?
        </h2>

        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="/trajets/create"
               class="btn btn-success px-4">
                Créer un trajet
            </a>
        <?php else: ?>
            <a href="/login?redirect=/trajets/create"
               class="btn btn-success px-4">
                Créer un trajet
            </a>
        <?php endif; ?>

    </div>
</section>
