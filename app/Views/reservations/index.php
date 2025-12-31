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

        <?php if ($r['etat'] === 'confirmé'): ?>
          <form method="get" action="/reservations/annuler" style="display:inline;">
            <input type="hidden" name="id" value="<?= (int)$r['participation_id'] ?>">
            <button type="submit">Annuler</button>
          </form>
        <?php endif; ?>
      </li>
      <hr>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
