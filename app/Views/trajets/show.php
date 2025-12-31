<h1>Détail du trajet</h1>

<p><strong>Départ :</strong> <?= htmlspecialchars($trajet['ville_depart']) ?></p>
<p><strong>Arrivée :</strong> <?= htmlspecialchars($trajet['ville_arrivee']) ?></p>

<p><strong>Date :</strong> <?= htmlspecialchars($trajet['date_depart']) ?></p>
<p><strong>Heure :</strong> <?= htmlspecialchars($trajet['heure_depart']) ?></p>

<p><strong>Prix :</strong> <?= (int)$trajet['prix'] ?> crédits</p>
<p><strong>Places restantes :</strong> <?= (int)$trajet['places_restantes'] ?></p>
<p><strong>Statut :</strong> <?= htmlspecialchars($trajet['statut']) ?></p>

<?php if (!empty($_SESSION['user'])): ?>
  <?php if ((int)$trajet['places_restantes'] > 0): ?>
    <a href="/trajets/reserver?id=<?= (int)$trajet['id'] ?>">
      Réserver
    </a>
  <?php else: ?>
    <p>Aucune place disponible.</p>
  <?php endif; ?>
<?php else: ?>
  <p><a href="/login">Se connecter pour réserver</a></p>
<?php endif; ?>
