<h1>EcoRide</h1>
<p>Covoiturage simple, économique, et plus responsable.</p>

<p>
  <a href="/trajets">Rechercher un trajet</a>
</p>

<?php if (!empty($_SESSION['user'])): ?>
  <p><a href="/reservations">Mes réservations</a></p>
  <p><a href="/logout">Déconnexion</a></p>
<?php else: ?>
  <p><a href="/login">Connexion</a></p>
<?php endif; ?>

<?php if (!empty($_SESSION['user']) && !empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
  <p><a href="/admin">Administration</a></p>
<?php endif; ?>
