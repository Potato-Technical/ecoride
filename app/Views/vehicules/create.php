<section class="container py-4">
    <h1 class="h4 mb-3">Ajouter un véhicule</h1>

    <form method="POST" action="/vehicules/store" class="d-grid gap-3">
        <input type="hidden" name="csrfToken" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div>
            <label class="form-label">Immatriculation</label>
            <input class="form-control" name="immatriculation" required>
        </div>

        <div>
            <label class="form-label">Date 1ère immatriculation</label>
            <input class="form-control" type="date" name="date_premiere_immatriculation" required>
        </div>

        <div>
            <label class="form-label">Marque</label>
            <input class="form-control" name="marque" required>
        </div>

        <div>
            <label class="form-label">Modèle</label>
            <input class="form-control" name="modele" required>
        </div>

        <div>
            <label class="form-label">Couleur</label>
            <input class="form-control" name="couleur" required>
        </div>

        <div>
            <label class="form-label">Énergie</label>
            <select class="form-select" name="energie" required>
                <option value="">-- Choisir --</option>
                <option value="essence">Essence</option>
                <option value="diesel">Diesel</option>
                <option value="hybride">Hybride</option>
                <option value="electrique">Électrique</option>
            </select>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="fumeur_accepte" id="fumeur">
            <label class="form-check-label" for="fumeur">Fumeur accepté</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="animaux_acceptes" id="animaux">
            <label class="form-check-label" for="animaux">Animaux acceptés</label>
        </div>

        <button class="btn btn-primary" type="submit">Enregistrer</button>
    </form>
</section>