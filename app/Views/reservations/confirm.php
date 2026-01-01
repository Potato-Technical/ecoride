<h1>Confirmer la réservation</h1>

<p>
    Trajet :
    <?= htmlspecialchars($trajet['lieu_depart']) ?>
    →
    <?= htmlspecialchars($trajet['lieu_arrivee']) ?>
</p>

<p>
    Date :
    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($trajet['date_heure_depart']))) ?>
</p>

<p>
    Prix :
    <?= (int) $trajet['prix'] ?> crédits
</p>

<form method="POST" action="/trajets/reserver/confirm">
    <input type="hidden" name="trajet_id" value="<?= (int) $trajet['id'] ?>">
    <button type="submit">Confirmer la réservation</button>
</form>

<p>
    <a href="/trajet?id=<?= (int) $trajet['id'] ?>">Annuler</a>
</p>
