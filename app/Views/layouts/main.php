<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <!-- Titre de la page -->
    <!-- Si $title existe, on l'affiche, sinon on utilise une valeur par défaut -->
    <title><?= $title ?? 'EcoRide' ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Injection du contenu HTML capturé depuis la vue -->
<!-- $content provient du contrôleur via ob_start / ob_get_clean -->
<div class="container mt-4">

<?php if (!empty($_SESSION['flash'])):
    $type = $_SESSION['flash']['type'] === 'error'
        ? 'danger'
        : $_SESSION['flash']['type'];
?>

<div class="alert alert-<?= htmlspecialchars($type) ?>" role="alert">
    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
</div>

<?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?= $content ?>

</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="app-toast" class="toast align-items-center" role="alert">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/reservations.js"></script>

</body>
</html>
