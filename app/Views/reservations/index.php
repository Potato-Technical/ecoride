<h1>Mes réservations</h1>

<?php if (empty($reservations)): ?>
  <p>Aucune réservation.</p>
<?php else: ?>
  <ul>
    <?php foreach ($reservations as $r): ?>
      <li>
        <strong><?= htmlspecialchars($r['lieu_depart']) ?></strong>
        →
        <strong><?= htmlspecialchars($r['lieu_arrivee']) ?></strong><br>

        Départ :
        <?= htmlspecialchars(date('d/m/Y H:i', strtotime($r['date_heure_depart']))) ?>
        — Prix : <?= (int)$r['prix'] ?> crédits<br>

        État : <strong><?= htmlspecialchars($r['etat']) ?></strong>

        <?php if ($r['etat'] === 'confirme'): ?>
          <form method="post" action="/reservation/cancel">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <button type="submit">Annuler</button>
          </form>
        <?php endif; ?>
      </li>
      <hr>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
