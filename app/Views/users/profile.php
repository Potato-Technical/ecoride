<h1 class="mb-4">Mon compte</h1>

<div class="card shadow-sm">
    <div class="card-body">

        <ul class="list-unstyled mb-4">
            <li class="mb-2"><strong>Pseudo :</strong> <?= htmlspecialchars($user['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
            <li class="mb-2"><strong>Email :</strong> <?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></li>
            <li class="mb-2"><strong>Rôle :</strong> <?= htmlspecialchars($roleLabel ?? ($_SESSION['role'] ?? ''), ENT_QUOTES, 'UTF-8') ?></li>
            <li class="mb-2"><strong>Crédits :</strong> <?= (int)($solde ?? 0) ?></li>
        </ul>

        <div class="d-grid gap-2">
            <a class="btn btn-outline-primary" href="/reservations">Mes réservations</a>
            <a class="btn btn-outline-primary" href="/vehicules">Mes véhicules</a>
            <a class="btn btn-outline-primary" href="/trajets/chauffeur">Mes trajets (chauffeur)</a>
        </div>

    </div>
</div>