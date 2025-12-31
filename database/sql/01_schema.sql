/*
 MPD — EcoRide
 SGBD : MySQL 8 / InnoDB / utf8mb4

 Choix structurants :
 - Aucun attribut dérivé stocké (solde crédits, places restantes).
 - Historisation financière via credit_mouvement.
 - Intégrité référentielle assurée par clés étrangères.
 - Les règles métier complexes sont gérées côté applicatif.
*/

-- Table : role
-- Rôle : définition des rôles fonctionnels de l’application
CREATE TABLE role (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE -- utilisateur / employe / administrateur
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table : utilisateur
-- Rôle : comptes utilisateurs (authentification, rôles, suspension)
CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL UNIQUE, -- nom public affiché
    email VARCHAR(100) NOT NULL UNIQUE, -- identifiant de connexion
    mot_de_passe_hash VARCHAR(255) NOT NULL, -- hash du mot de passe
    photo VARCHAR(255), -- avatar optionnel
    est_suspendu BOOLEAN NOT NULL DEFAULT FALSE, -- désactivation logique du compte
    role_id INT NOT NULL, -- rôle fonctionnel de l'utilisateur
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- date de création du compte
    updated_at DATETIME DEFAULT NULL, -- date de dernière modification du compte

    KEY idx_utilisateur_role (role_id),
    FOREIGN KEY (role_id) REFERENCES role(id) -- attribution du rôle
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table : vehicule
-- Rôle : véhicules enregistrés par les chauffeurs
CREATE TABLE vehicule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    immatriculation VARCHAR(20) NOT NULL UNIQUE, -- identifiant légal du véhicule
    date_premiere_immatriculation DATE NOT NULL,
    modele VARCHAR(100) NOT NULL,
    marque VARCHAR(100) NOT NULL,
    couleur VARCHAR(50) NOT NULL,
    energie ENUM('thermique','electrique','hybride') NOT NULL, -- type d’énergie du véhicule
    fumeur_accepte BOOLEAN NOT NULL,
    animaux_acceptes BOOLEAN NOT NULL,
    preferences_libres TEXT, -- préférences complémentaires du chauffeur
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
    date_heure_arrivee DATETIME, -- renseignée à la fin du trajet
    prix DECIMAL(10,2) NOT NULL,
    nb_places INT NOT NULL, -- capacité totale du véhicule
    statut VARCHAR(30) NOT NULL, -- cycle de vie du trajet
    chauffeur_id INT NOT NULL, -- utilisateur conducteur
    vehicule_id INT NOT NULL, -- véhicule utilisé
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- date de création du trajet

    CHECK (statut IN ('planifie','demarre','termine','annule')), -- domaine autorisé

    KEY idx_trajet_chauffeur (chauffeur_id),
    KEY idx_trajet_vehicule (vehicule_id),
    FOREIGN KEY (chauffeur_id) REFERENCES utilisateur(id),
    FOREIGN KEY (vehicule_id) REFERENCES vehicule(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table : participation
-- Rôle : réservation d’un passager sur un trajet
-- Entité-association Utilisateur ↔ Trajet
CREATE TABLE participation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    etat VARCHAR(30) NOT NULL, -- demandé / confirmé / annulé
    confirme_le DATETIME, -- renseigné uniquement si confirmé
    credits_utilises INT NOT NULL, -- débit appliqué au passager
    utilisateur_id INT NOT NULL, -- passager
    trajet_id INT NOT NULL, -- trajet réservé
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- date de la demande de participation

    CHECK (etat IN ('demande','confirme','annule')),
    UNIQUE (utilisateur_id, trajet_id), -- une seule réservation par utilisateur et trajet

    KEY idx_participation_utilisateur (utilisateur_id),
    KEY idx_participation_trajet (trajet_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id),
    FOREIGN KEY (trajet_id) REFERENCES trajet(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table : avis
-- Rôle : évaluations post-trajet
CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note INT NOT NULL, -- note attribuée au chauffeur
    commentaire TEXT,
    statut_validation VARCHAR(30) NOT NULL, -- modération employé
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


-- Table : credit_mouvement
-- Rôle : historique financier (débits / crédits)
-- Le solde utilisateur est calculé dynamiquement
CREATE TABLE credit_mouvement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL, -- nature du mouvement
    montant INT NOT NULL, -- valeur positive ou négative
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- date du mouvement de crédit
    utilisateur_id INT NOT NULL, -- compte impacté
    participation_id INT, -- lien optionnel avec une réservation

    CHECK (type IN (
        'creation_compte',
        'debit_reservation',
        'credit_trajet',
        'commission_plateforme',
        'remboursement'
    )),

    KEY idx_credit_utilisateur (utilisateur_id),
    KEY idx_credit_participation (participation_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id),
    FOREIGN KEY (participation_id) REFERENCES participation(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table : notification_mail
-- Rôle : journalisation des notifications envoyées
CREATE TABLE notification_mail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL, -- confirmation, annulation, demande_validation
    sujet VARCHAR(255) NOT NULL,
    corps TEXT NOT NULL,
    date_envoi DATETIME NOT NULL,
    utilisateur_id INT NOT NULL, -- destinataire

    KEY idx_notification_utilisateur (utilisateur_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
