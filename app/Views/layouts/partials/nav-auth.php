<nav class="navbar navbar-expand-lg navbar-light bg-warning-subtle border-bottom">
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
            <ul class="navbar-nav ms-auto gap-lg-3">

                <!-- Chauffeur -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        Chauffeur
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="/trajets/create">
                                Créer un trajet
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/trajets/chauffeur">
                                Mes trajets
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Passager -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        Passager
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="/trajets">
                                Trajets
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/reservations">
                                Mes réservations
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Compte -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        Compte
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="/vehicules">
                                Mes véhicules
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/profil">
                                Mon compte
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="/logout" class="px-3">
                                <input type="hidden"
                                       name="csrfToken"
                                       value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit"
                                        class="btn btn-link text-danger p-0">
                                    Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>

    </div>
</nav>