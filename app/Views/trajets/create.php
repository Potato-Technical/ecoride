<h1 class="mb-4">Créer un trajet</h1>

<form method="post" class="card p-4 shadow-sm">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

    <div class="mb-3">
        <label class="form-label">Lieu de départ</label>
        <input type="text" name="lieu_depart" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Lieu d’arrivée</label>
        <input type="text" name="lieu_arrivee" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Date et heure de départ</label>
        <input type="datetime-local" name="date_heure_depart" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Prix (crédits)</label>
        <input type="number" name="prix" min="1" class="form-control" required>
    </div>

    <div class="mb-4">
        <label class="form-label">Nombre de places</label>
        <input type="number" name="nb_places" min="1" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Créer le trajet</button>
</form>
