<?php
/** @var array $user */
/** @var int $solde */
/** @var string $roleLabel */
/** @var bool $hasVehicule */
?>

<section class="profile-page">
    <header class="profile-hero">
        <div class="profile-avatar" aria-hidden="true">
            <?= strtoupper(substr($user['pseudo'] ?? 'U', 0, 1)) ?>
        </div>

        <div class="profile-hero-content">
            <p class="profile-kicker">Espace personnel</p>
            <h1 class="profile-title">Mon compte</h1>
            <p class="profile-subtitle">
                Consultez vos informations et gérez votre activité sur EcoRide.
            </p>
        </div>
    </header>

    <section class="profile-card" aria-labelledby="profile-infos-title">
        <div class="profile-card-head">
            <h2 id="profile-infos-title">Mes informations</h2>
        </div>

        <div class="profile-list">
            <div class="profile-item">
                <span class="profile-item-label">Pseudo</span>
                <strong class="profile-item-value">
                    <?= htmlspecialchars($user['pseudo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </strong>
            </div>

            <div class="profile-item">
                <span class="profile-item-label">Email</span>
                <strong class="profile-item-value">
                    <?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </strong>
            </div>

            <div class="profile-item">
                <span class="profile-item-label">Rôle</span>
                <strong class="profile-item-value">
                    <?= htmlspecialchars($roleLabel ?? 'utilisateur', ENT_QUOTES, 'UTF-8') ?>
                </strong>
            </div>

            <div class="profile-item">
                <span class="profile-item-label">Crédits</span>
                <strong class="profile-credit">
                    <?= (int)($solde ?? 0) ?> crédits
                </strong>
            </div>
        </div>
    </section>

    <section class="profile-card" aria-labelledby="profile-activity-title">
        <div class="profile-card-head">
            <h2 id="profile-activity-title">Mon activité</h2>
        </div>

        <nav class="profile-menu" aria-label="Navigation du compte">
            <a class="profile-link" href="/reservations">
                <div class="profile-link-body">
                    <span class="profile-link-title">Mes réservations</span>
                    <span class="profile-link-text">Voir les trajets réservés</span>
                </div>
                <span class="profile-link-arrow" aria-hidden="true">›</span>
            </a>

            <a class="profile-link" href="/historique">
                <div class="profile-link-body">
                    <span class="profile-link-title">Historique</span>
                    <span class="profile-link-text">Retrouver votre activité passée</span>
                </div>
                <span class="profile-link-arrow" aria-hidden="true">›</span>
            </a>

            <a class="profile-link" href="/vehicules">
                <div class="profile-link-body">
                    <span class="profile-link-title">Mes véhicules</span>
                    <span class="profile-link-text">Ajouter, modifier ou supprimer un véhicule</span>
                </div>
                <span class="profile-link-arrow" aria-hidden="true">›</span>
            </a>

            <?php if (!empty($hasVehicule)): ?>
                <a class="profile-link" href="/trajets/chauffeur">
                    <div class="profile-link-body">
                        <span class="profile-link-title">Mes trajets chauffeur</span>
                        <span class="profile-link-text">Gérer les trajets que vous publiez</span>
                    </div>
                    <span class="profile-link-arrow" aria-hidden="true">›</span>
                </a>

                <a class="profile-link profile-link--primary" href="/trajets/create">
                    <div class="profile-link-body">
                        <span class="profile-link-title">Publier un trajet</span>
                        <span class="profile-link-text">Créer un nouveau trajet avec votre véhicule</span>
                    </div>
                    <span class="profile-link-arrow" aria-hidden="true">›</span>
                </a>
            <?php else: ?>
                <a class="profile-link profile-link--primary" href="/vehicules/create">
                    <div class="profile-link-body">
                        <span class="profile-link-title">Ajouter un véhicule</span>
                        <span class="profile-link-text">Nécessaire pour publier un trajet</span>
                    </div>
                    <span class="profile-link-arrow" aria-hidden="true">›</span>
                </a>
            <?php endif; ?>
        </nav>
    </section>
</section>