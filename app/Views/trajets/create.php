<h1>Créer un trajet</h1>

<!-- Formulaire de création d’un trajet -->
<form method="post">
    <input type="text" name="lieu_depart" placeholder="Départ" required>
    <input type="text" name="lieu_arrivee" placeholder="Arrivée" required>
    <input type="datetime-local" name="date_heure_depart" required>
    <input type="number" name="prix" required>
    <input type="number" name="nb_places" required>
    <button type="submit">Créer</button>
</form>
