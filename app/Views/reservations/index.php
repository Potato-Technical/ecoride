<h1>Mes réservations</h1>

<?php if (empty($reservations)): ?>
  <p>Aucune réservation.</p>
<?php else: ?>
  <ul>
    <?php foreach ($reservations as $r): ?>
      <li>
        <strong><?= htmlspecialchars((string) ($r['lieu_depart'] ?? '')) ?></strong>
        →
        <strong><?= htmlspecialchars((string) ($r['lieu_arrivee'] ?? '')) ?></strong><br>

        Départ :
        <?= htmlspecialchars(date('d/m/Y H:i', strtotime($r['date_heure_depart']))) ?>
        — Prix : <?= (int)$r['prix'] ?> crédits<br>

        État : <strong><?= htmlspecialchars((string) ($r['etat'] ?? '')) ?></strong>

        <?php if ($r['etat'] === 'confirme'): ?>
          <form method="POST" action="/reservations/annuler" class="d-inline">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm">
                  Annuler
              </button>
          </form>
        <?php else: ?>
            <span class="text-muted">
                Annulation indisponible
            </span>
        <?php endif; ?>
      </li>
      <hr>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
