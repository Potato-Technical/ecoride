<h1>Détail du trajet</h1>

<p><strong>Départ :</strong> <?= htmlspecialchars($trajet['lieu_depart']) ?></p>
<p><strong>Arrivée :</strong> <?= htmlspecialchars($trajet['lieu_arrivee']) ?></p>

<p><strong>Date :</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($trajet['date_heure_depart']))) ?></p>
<p><strong>Heure :</strong> <?= htmlspecialchars(date('H:i', strtotime($trajet['date_heure_depart']))) ?></p>

<p><strong>Prix :</strong> <?= (int)$trajet['prix'] ?> crédits</p>
<p><strong>Places restantes :</strong> <?= (int)$trajet['nb_places'] ?></p>
<p><strong>Statut :</strong> <?= htmlspecialchars($trajet['statut']) ?></p>

<hr>

<?php if (empty($_SESSION['user_id'])): ?>

<p>
    <a href="/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-outline-primary">
        Se connecter pour réserver
    </a>
</p>

<?php elseif ($hasParticipation): ?>

<button class="btn btn-secondary" disabled>Déjà réservé</button>

<?php elseif ((int)$trajet['nb_places'] <= 0): ?>

<button class="btn btn-secondary" disabled>Trajet complet</button>

<?php else: ?>

<form method="POST"
      action="/trajets/reserver"
      class="d-inline js-reserve-form">

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <input type="hidden" name="trajet_id" value="<?= (int)$trajet['id'] ?>">

    <button type="submit"
            class="btn btn-primary"
            data-original-text="Réserver">
        Réserver
    </button>
</form>

<?php endif; ?>
