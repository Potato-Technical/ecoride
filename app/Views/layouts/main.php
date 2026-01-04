<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <!-- Titre de la page -->
    <!-- Si $title existe, on l'affiche, sinon on utilise une valeur par défaut -->
    <title><?= $title ?? 'EcoRide' ?></title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body>

<!--
  Injection du contenu HTML capturé depuis la vue
  $content provient du contrôleur via ob_start / ob_get_clean
-->
<div class="container mt-4">

    <?= $content ?>

</div>

<!--
  Toast Bootstrap global
  Utilisé pour les feedbacks asynchrones (AJAX)
  Ex : annulation de réservation sans rechargement de page
-->
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- JS métier : réservations (AJAX annulation) -->
<script src="/assets/js/reservations.js"></script>

</body>
</html>
