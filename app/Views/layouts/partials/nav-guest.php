<nav class="navbar navbar-expand-lg navbar-light border-bottom">
  <div class="container">

    <a class="navbar-brand fw-semibold" href="/">EcoRide</a>

    <button class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto gap-lg-3 align-items-lg-center">

        <li class="nav-item">
          <a href="/" class="nav-link <?= active('/') ?>">
            Accueil
          </a>
        </li>

        <li class="nav-item">
          <a href="/trajets" class="nav-link <?= active('/trajets') ?>">
            Covoiturages
          </a>
        </li>

        <li class="nav-item">
          <a href="/a-propos" class="nav-link <?= active('/a-propos') ?>">
            À propos
          </a>
        </li>

        <li class="nav-item">
          <a href="/contact" class="nav-link <?= active('/contact') ?>">
            Contact
          </a>
        </li>

        <li class="nav-item ms-lg-2">
          <a href="/login" class="btn btn-sm nav-cta">
            Connexion
          </a>
        </li>

      </ul>
    </div>

  </div>
</nav>
