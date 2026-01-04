<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'EcoRide' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex flex-column min-vh-100">
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand" href="/">EcoRide</a>

            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav gap-2">

                    <li class="nav-item">
                        <a class="nav-link" href="/trajets">Trajets</a>
                    </li>

                    <?php if (!empty($_SESSION['user_id'])): ?>

                        <li class="nav-item">
                            <a class="nav-link" href="/reservations">Mes réservations</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/logout">Déconnexion</a>
                        </li>

                        <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'administrateur'): ?>
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="/admin">Administration</a>
                            </li>
                        <?php endif; ?>

                    <?php else: ?>

                        <li class="nav-item">
                            <a class="nav-link" href="/login">Connexion</a>
                        </li>

                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="flex-fill">
    <div class="container mt-4">
        <?= $content ?>
    </div>
</main>

<footer class="bg-dark text-light mt-5">
    <div class="container py-3 text-center small">
        © EcoRide 2025 — Mentions légales · CGU · Accessibilité
    </div>
</footer>

<!-- Toast global (PASSIF) -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="app-toast" class="toast align-items-center" role="alert">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button"
                    class="btn-close me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if (!empty($scripts)): ?>
    <?php foreach ($scripts as $script): ?>
        <script src="<?= htmlspecialchars($script) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
