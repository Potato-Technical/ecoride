<h1 class="mb-4 text-center">Créer un trajet</h1>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <form method="POST" class="card shadow-sm">
            <div class="card-body">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="mb-3">
                    <label for="lieu_depart" class="form-label">
                        Lieu de départ
                    </label>
                    <input type="text"
                           id="lieu_depart"
                           name="lieu_depart"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label for="lieu_arrivee" class="form-label">
                        Lieu d’arrivée
                    </label>
                    <input type="text"
                           id="lieu_arrivee"
                           name="lieu_arrivee"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label for="date_heure_depart" class="form-label">
                        Date et heure de départ
                    </label>
                    <input type="datetime-local"
                           id="date_heure_depart"
                           name="date_heure_depart"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label for="prix" class="form-label">
                        Prix
                    </label>
                    <input type="number"
                           id="prix"
                           name="prix"
                           min="1"
                           class="form-control"
                           required>
                </div>

                <div class="mb-4">
                    <label for="nb_places" class="form-label">
                        Nombre de places
                    </label>
                    <input type="number"
                           id="nb_places"
                           name="nb_places"
                           min="1"
                           class="form-control"
                           required>
                </div>

                <div class="d-grid">
                    <button type="submit"
                            class="btn btn-primary">
                        Créer le trajet
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>
