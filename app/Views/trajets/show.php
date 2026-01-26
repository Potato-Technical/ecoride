<h1 class="mb-4">Détail du trajet</h1>

<div class="card shadow-sm mb-4">
    <div class="card-body">

        <h2 class="h5 mb-3">
            <?= htmlspecialchars($trajet['lieu_depart']) ?>
            →
            <?= htmlspecialchars($trajet['lieu_arrivee']) ?>
        </h2>

        <ul class="list-unstyled mb-4">
            <li class="mb-2">
                <strong>Date :</strong>
                <?= htmlspecialchars(date('d/m/Y', strtotime($trajet['date_heure_depart']))) ?>
            </li>
            <li class="mb-2">
                <strong>Heure :</strong>
                <?= htmlspecialchars(date('H:i', strtotime($trajet['date_heure_depart']))) ?>
            </li>
            <li class="mb-2">
                <strong>Prix :</strong>
                <?= (int) $trajet['prix'] ?> crédits
            </li>
            <li class="mb-2">
                <strong>Places restantes :</strong>
                <?= (int) $trajet['nb_places'] ?>
            </li>
            <li>
                <strong>Statut :</strong>
                <?= htmlspecialchars($trajet['statut']) ?>
            </li>
        </ul>

        <hr>

        <div class="mt-3">

            <?php if (empty($_SESSION['user_id'])): ?>

                <a href="/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                   class="btn btn-outline-primary">
                    Se connecter pour réserver
                </a>

            <?php elseif ($hasParticipation): ?>

                <button class="btn btn-secondary" disabled>
                    Déjà réservé
                </button>

            <?php elseif ((int) $trajet['nb_places'] <= 0): ?>

                <button class="btn btn-secondary" disabled>
                    Trajet complet
                </button>

            <?php else: ?>

                <form method="POST"
                      action="/trajets/reserver"
                      class="d-inline js-reserve-form">

                    <input type="hidden"
                           name="csrf_token"
                           value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    <input type="hidden"
                           name="trajet_id"
                           value="<?= (int) $trajet['id'] ?>">

                    <button type="submit"
                            class="btn btn-primary"
                            data-original-text="Réserver">
                        Réserver
                    </button>
                </form>

            <?php endif; ?>

        </div>

    </div>
</div>
