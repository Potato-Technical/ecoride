<h1 class="mb-4">Confirmer la réservation</h1>

<div class="card shadow-sm">
    <div class="card-body">

        <h2 class="h6 mb-3">
            <?= htmlspecialchars($trajet['lieu_depart']) ?>
            →
            <?= htmlspecialchars($trajet['lieu_arrivee']) ?>
        </h2>

        <ul class="list-unstyled mb-4">
            <li class="mb-2">
                <strong>Date :</strong>
                <?= htmlspecialchars(date('d/m/Y H:i', strtotime($trajet['date_heure_depart']))) ?>
            </li>
            <li>
                <strong>Prix :</strong>
                <?= (int) $trajet['prix'] ?> crédits
            </li>
        </ul>

        <div class="d-flex gap-2">

            <form method="POST"
                  action="/trajets/reserver/confirm"
                  class="js-reserve-form">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= htmlspecialchars($csrf_token) ?>">

                <input type="hidden"
                       name="trajet_id"
                       value="<?= (int) $trajet['id'] ?>">

                <button type="submit"
                        class="btn btn-primary">
                    Confirmer
                </button>
            </form>

            <a href="/trajet?id=<?= (int) $trajet['id'] ?>"
               class="btn btn-outline-secondary">
                Annuler
            </a>

        </div>

    </div>
</div>
