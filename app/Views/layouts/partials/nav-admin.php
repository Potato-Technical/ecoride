<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">

        <a class="navbar-brand fw-semibold" href="/admin">EcoRide</a>

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
                
                <li class="nav-item">
                    <a href="/admin" class="nav-link">Dashboard</a>
                </li>

                <li class="nav-item">
                    <a href="/trajets" class="nav-link">Trajets</a>
                </li>

                <li class="nav-item">
                    <form method="POST" action="/logout" class="nav-link text-danger">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="btn btn-link nav-link text-danger p-0">
                            Déconnexion
                        </button>
                    </form>
                </li>

            </ul>
        </div>

    </div>
</nav>
