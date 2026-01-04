<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'EcoRide' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex flex-column min-vh-100">

<header class="bg-white border-bottom">
    <div class="container py-3">
        <strong>EcoRide</strong>
    </div>
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
            <button
                type="button"
                class="btn-close me-2 m-auto"
                data-bs-dismiss="toast"
            ></button>
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
