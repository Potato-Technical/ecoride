<div class="form-shell">
    <h1 class="form-page-title">Créer un trajet</h1>
    <p class="trajet-form-intro">
        Renseignez les informations principales de votre trajet.
    </p>

    <form method="POST" class="form-card">
        <?= csrf_field() ?>

        <div class="form-field">
            <label for="lieu_depart" class="form-label">Lieu de départ</label>
            <input
                type="text"
                id="lieu_depart"
                name="lieu_depart"
                class="form-input"
                required>
        </div>

        <div class="form-field">
            <label for="lieu_arrivee" class="form-label">Lieu d’arrivée</label>
            <input
                type="text"
                id="lieu_arrivee"
                name="lieu_arrivee"
                class="form-input"
                required>
        </div>

        <div class="form-field">
            <label for="date_heure_depart" class="form-label">Date et heure de départ</label>
            <input
                type="datetime-local"
                id="date_heure_depart"
                name="date_heure_depart"
                class="form-input"
                required>
        </div>

        <div class="form-field">
            <label for="duree_estimee_minutes" class="form-label">Durée estimée (en minutes)</label>
            <input
                type="number"
                id="duree_estimee_minutes"
                name="duree_estimee_minutes"
                min="1"
                class="form-input"
                required>
        </div>

        <div class="form-field">
            <label for="vehicule_id" class="form-label">Véhicule</label>
            <select id="vehicule_id" name="vehicule_id" class="form-select" required>
                <option value="">Choisir</option>
                <?php foreach (($vehicules ?? []) as $v): ?>
                    <option value="<?= (int)$v['id'] ?>">
                        <?= htmlspecialchars(
                            ($v['immatriculation'] ?? '') . ' - ' .
                            ($v['marque'] ?? '') . ' ' .
                            ($v['modele'] ?? '') . ' (' .
                            ($v['energie'] ?? '') . ')',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="trajet-form-grid">
            <div class="form-field">
                <label for="prix" class="form-label">Prix</label>
                <input
                    type="number"
                    id="prix"
                    name="prix"
                    min="1"
                    class="form-input"
                    required>
            </div>

            <div class="form-field">
                <label for="nb_places" class="form-label">Nombre de places</label>
                <input
                    type="number"
                    id="nb_places"
                    name="nb_places"
                    min="1"
                    class="form-input"
                    required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary-ui">Créer le trajet</button>
            <a href="/trajets/chauffeur" class="btn-secondary-ui">Annuler</a>
        </div>
    </form>
</div>