<h1 class="form-page-title">Ajouter un véhicule</h1>

<div class="form-shell">
    <form method="POST" action="/vehicules/store" class="form-card">
        <?= csrf_field() ?>

        <div class="form-field">
            <label class="form-label">Immatriculation</label>
            <input class="form-input" name="immatriculation" required>
        </div>

        <div class="form-field">
            <label class="form-label">Date 1ère immatriculation</label>
            <input type="date" class="form-input" name="date_premiere_immatriculation" required>
        </div>

        <div class="form-field">
            <label class="form-label">Marque</label>
            <input class="form-input" name="marque" required>
        </div>

        <div class="form-field">
            <label class="form-label">Modèle</label>
            <input class="form-input" name="modele" required>
        </div>

        <div class="form-field">
            <label class="form-label">Couleur</label>
            <input class="form-input" name="couleur" required>
        </div>

        <div class="form-field">
            <label class="form-label">Énergie</label>
            <select class="form-select" name="energie" required>
                <option value="">Choisir</option>
                <option value="thermique">Thermique</option>
                <option value="hybride">Hybride</option>
                <option value="electrique">Électrique</option>
            </select>
        </div>

        <div class="form-field">
            <label class="form-label">Préférences libres</label>
            <input class="form-input" name="preferences_libres">
        </div>

        <div class="form-checkgroup">
            <label class="form-check">
                <input type="checkbox" name="fumeur">
                <span>Fumeur accepté</span>
            </label>

            <label class="form-check">
                <input type="checkbox" name="animaux">
                <span>Animaux acceptés</span>
            </label>
        </div>

        <div class="form-actions">
            <button class="btn-primary-ui" type="submit">Enregistrer</button>
            <a href="/vehicules" class="btn-secondary-ui">Annuler</a>
        </div>
    </form>
</div>