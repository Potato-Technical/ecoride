/*
 MPD — EcoRide
 SGBD : MySQL 8 / InnoDB / utf8mb4

 Choix structurants (version cible) :
 - Le solde officiel est stocké : utilisateur.credits (DEFAULT 20 à l’inscription).
 - Les places restantes sont stockées : trajet.places_restantes (initialisée à nb_places).
 - Historisation financière via credit_mouvement (trace, pas source de vérité du solde).
 - Idempotence paiement chauffeur : trajet.paid_at (paiement 1 seule fois).
 - Préférences chauffeur portées par vehicule (pas de table preferences).
 - Intégrité référentielle assurée par clés étrangères.
 - Les règles métier (transactions, remboursements, idempotence) sont gérées côté applicatif.
*/

-- Table : role
-- Rôle : définition des rôles fonctionnels de l’application
CREATE TABLE role (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE -- utilisateur / employe / administrateur
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table : utilisateur
-- Rôle : comptes utilisateurs (authentification, rôles, suspension, crédits)
CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL UNIQUE, -- nom public affiché
    email VARCHAR(100) NOT NULL UNIQUE, -- identifiant de connexion
    mot_de_passe_hash VARCHAR(255) NOT NULL, -- hash du mot de passe
    photo VARCHAR(255), -- avatar optionnel
    est_suspendu BOOLEAN NOT NULL DEFAULT FALSE, -- désactivation logique du compte
    credits INT NOT NULL DEFAULT 0,
    role_id INT NOT NULL, -- rôle fonctionnel de l'utilisateur
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- date de création du compte
    updated_at DATETIME DEFAULT NULL, -- date de dernière modification du compte

    KEY idx_utilisateur_role (role_id),
    KEY idx_utilisateur_email (email),
    FOREIGN KEY (role_id) REFERENCES role(id) -- attribution du rôle
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table : vehicule
-- Rôle : véhicules enregistrés par les chauffeurs + préférences portées par véhicule
CREATE TABLE vehicule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    immatriculation VARCHAR(20) NOT NULL UNIQUE, -- identifiant légal du véhicule
    date_premiere_immatriculation DATE NOT NULL,
    modele VARCHAR(100) NOT NULL,
    marque VARCHAR(100) NOT NULL,
    couleur VARCHAR(50) NOT NULL,
    energie ENUM('thermique','electrique','hybride') NOT NULL, -- type d’énergie du véhicule
    fumeur BOOLEAN NOT NULL DEFAULT 0, -- préférence (US5/US8)
    animaux BOOLEAN NOT NULL DEFAULT 0, -- préférence (US5/US8)
    preferences_libres VARCHAR(255) DEFAULT NULL, -- préférence libre (US5/US8)
    utilisateur_id INT NOT NULL, -- propriétaire du véhicule
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- date d’enregistrement du véhicule

    KEY idx_vehicule_utilisateur (utilisateur_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table : trajet
-- Rôle : trajets proposés par les chauffeurs
CREATE TABLE trajet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lieu_depart VARCHAR(150) NOT NULL,
    lieu_arrivee VARCHAR(150) NOT NULL,
    date_heure_depart DATETIME NOT NULL,
    date_heure_arrivee DATETIME DEFAULT NULL, -- renseignée à la fin du trajet
    prix DECIMAL(10,2) NOT NULL,
    nb_places INT NOT NULL, -- capacité totale du véhicule
    places_restantes INT NOT NULL, -- stocké (init = nb_places) (US6/US10)
    statut VARCHAR(30) NOT NULL, -- cycle de vie du trajet
    paid_at DATETIME DEFAULT NULL, -- idempotence paiement chauffeur (US11)
    chauffeur_id INT NOT NULL, -- utilisateur conducteur
    vehicule_id INT NOT NULL, -- véhicule utilisé
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- date de création du trajet

    CHECK (statut IN ('planifie','demarre','termine','annule')),

    KEY idx_trajet_chauffeur (chauffeur_id),
    KEY idx_trajet_vehicule (vehicule_id),
    KEY idx_trajet_date_depart (date_heure_depart),
    KEY idx_trajet_recherche (lieu_depart, lieu_arrivee, date_heure_depart),

    FOREIGN KEY (chauffeur_id) REFERENCES utilisateur(id),
    FOREIGN KEY (vehicule_id) REFERENCES vehicule(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table : participation
-- Rôle : réservation d’un passager sur un trajet (une participation par user+trajet)
CREATE TABLE participation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    etat VARCHAR(30) NOT NULL, -- demande / confirme / annule
    confirme_le DATETIME DEFAULT NULL, -- renseigné uniquement si confirmé
    credits_utilises INT NOT NULL, -- snapshot du prix payé par le passager
    utilisateur_id INT NOT NULL, -- passager
    trajet_id INT NOT NULL, -- trajet réservé
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- date de la demande

    CHECK (etat IN ('demande','confirme','annule')),
    UNIQUE (utilisateur_id, trajet_id), -- choix : pas de re-réservation sur le même trajet

    KEY idx_participation_utilisateur (utilisateur_id),
    KEY idx_participation_trajet (trajet_id),

    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id),
    FOREIGN KEY (trajet_id) REFERENCES trajet(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table : avis
-- Rôle : évaluations post-trajet (modération employé)
CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note INT NOT NULL, -- note attribuée au chauffeur
    commentaire TEXT,
    statut_validation VARCHAR(30) NOT NULL, -- en_attente / valide / refuse
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- date de création de l’avis
    auteur_id INT NOT NULL, -- passager auteur de l’avis
    cible_id INT NOT NULL, -- chauffeur évalué
    trajet_id INT NOT NULL, -- trajet concerné

    CHECK (statut_validation IN ('en_attente','valide','refuse')),
    UNIQUE (trajet_id, auteur_id, cible_id), -- un avis par auteur et trajet

    KEY idx_avis_trajet (trajet_id),
    KEY idx_avis_auteur (auteur_id),
    KEY idx_avis_cible (cible_id),

    FOREIGN KEY (auteur_id) REFERENCES utilisateur(id),
    FOREIGN KEY (cible_id) REFERENCES utilisateur(id),
    FOREIGN KEY (trajet_id) REFERENCES trajet(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table : incident
-- Rôle : signalement passager OK/KO + traitement employé (US11/US12)
CREATE TABLE incident (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trajet_id INT NOT NULL,
    passager_id INT NOT NULL,
    chauffeur_id INT NOT NULL,
    etat ENUM('ok','ko') NOT NULL, -- déclaration passager
    description TEXT DEFAULT NULL, -- requis si ko (règle applicative)
    statut ENUM('ouvert','en_cours','resolu','rejete') NOT NULL DEFAULT 'ouvert', -- workflow employé
    handled_by INT DEFAULT NULL, -- employé (utilisateur.id)
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resolved_at DATETIME DEFAULT NULL,

    UNIQUE (trajet_id, passager_id), -- un signalement par passager et trajet

    KEY idx_incident_trajet (trajet_id),
    KEY idx_incident_passager (passager_id),
    KEY idx_incident_chauffeur (chauffeur_id),
    KEY idx_incident_handled_by (handled_by),

    FOREIGN KEY (trajet_id) REFERENCES trajet(id),
    FOREIGN KEY (passager_id) REFERENCES utilisateur(id),
    FOREIGN KEY (chauffeur_id) REFERENCES utilisateur(id),
    FOREIGN KEY (handled_by) REFERENCES utilisateur(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table : credit_mouvement
-- Rôle : historique financier (trace) — le solde officiel reste utilisateur.credits
CREATE TABLE credit_mouvement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL, -- nature du mouvement
    montant INT NOT NULL, -- valeur positive ou négative
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    utilisateur_id INT NOT NULL, -- compte impacté
    participation_id INT DEFAULT NULL, -- lien optionnel réservation
    trajet_id INT DEFAULT NULL, -- lien optionnel trajet (commission/stats)

    CHECK (type IN (
        'credit_initial',
        'creation_compte',
        'debit_reservation',
        'credit_trajet',
        'commission_plateforme',
        'remboursement'
    )),

    KEY idx_credit_utilisateur (utilisateur_id),
    KEY idx_credit_participation (participation_id),
    KEY idx_credit_trajet (trajet_id),
    KEY idx_credit_type_date (type, created_at),

    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id),
    FOREIGN KEY (participation_id) REFERENCES participation(id),
    FOREIGN KEY (trajet_id) REFERENCES trajet(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table : notification_mail
-- Rôle : journalisation des notifications envoyées (optionnel)
CREATE TABLE notification_mail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL, -- confirmation, annulation, demande_validation...
    sujet VARCHAR(255) NOT NULL,
    corps TEXT NOT NULL,
    date_envoi DATETIME NOT NULL,
    utilisateur_id INT NOT NULL, -- destinataire

    KEY idx_notification_utilisateur (utilisateur_id),
    KEY idx_notification_type_date (type, date_envoi),

    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
