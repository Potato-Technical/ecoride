<h1>Détail du trajet</h1>

<p><strong>Départ :</strong> <?= htmlspecialchars($trajet['lieu_depart']) ?></p>
<p><strong>Arrivée :</strong> <?= htmlspecialchars($trajet['lieu_arrivee']) ?></p>

<p><strong>Date :</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($trajet['date_heure_depart']))) ?></p>
<p><strong>Heure :</strong> <?= htmlspecialchars(date('H:i', strtotime($trajet['date_heure_depart']))) ?></p>

<p><strong>Prix :</strong> <?= (int)$trajet['prix'] ?> crédits</p>
<p><strong>Places restantes :</strong> <?= (int)$trajet['nb_places'] ?></p>
<p><strong>Statut :</strong> <?= htmlspecialchars($trajet['statut']) ?></p>

<?php if (!empty($_SESSION['user_id'])): ?>
  <?php if ($hasParticipation): ?>
    <p><em>Vous avez déjà participé à ce trajet.</em></p>
  <?php elseif ((int)$trajet['nb_places'] > 0): ?>
    <a href="/trajets/reserver?id=<?= (int)$trajet['id'] ?>">
      Réserver
    </a>
  <?php else: ?>
    <p>Aucune place disponible.</p>
  <?php endif; ?>
<?php else: ?>
  <p><a href="/login">Se connecter pour réserver</a></p>
<?php endif; ?>
