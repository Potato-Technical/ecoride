-- ECORIDE - structure.sql
-- Script de création de la base MySQL complète

-- Sécurité : suppression si déjà existant
DROP TABLE IF EXISTS reservation;
DROP TABLE IF EXISTS vehicule;
DROP TABLE IF EXISTS trajet;
DROP TABLE IF EXISTS utilisateur;

-- Table : utilisateur
CREATE TABLE utilisateur (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    role ENUM('passager', 'conducteur', 'employe', 'admin') DEFAULT 'passager',
    credits INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_utilisateur_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table : trajet
CREATE TABLE trajet (
    id_trajet INT AUTO_INCREMENT PRIMARY KEY,
    id_conducteur INT NOT NULL,
    ville_depart VARCHAR(100) NOT NULL,
    ville_arrivee VARCHAR(100) NOT NULL,
    date_depart DATE NOT NULL,
    heure_depart TIME NOT NULL,
    nb_places INT NOT NULL,
    description TEXT,
    prix DECIMAL(6,2) NOT NULL,
    is_eco TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    -- Clé étrangère : lien vers utilisateur
    CONSTRAINT fk_trajet_conducteur
        FOREIGN KEY (id_conducteur)
        REFERENCES utilisateur(id_user)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table : vehicule
CREATE TABLE vehicule (
    id_vehicule INT AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(100) NOT NULL,
    immatriculation VARCHAR(20) NOT NULL UNIQUE,
    nb_places TINYINT NOT NULL CHECK (nb_places > 0),
    proprietaire INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    -- Clé étrangère : lien vers utilisateur
    CONSTRAINT fk_vehicule_user
        FOREIGN KEY (proprietaire)
        REFERENCES utilisateur(id_user)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table : reservation
CREATE TABLE reservation (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_trajet INT NOT NULL,
    date_reservation DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('confirmée','annulée','effectué') DEFAULT 'confirmée',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    -- Clés étrangères
    CONSTRAINT fk_reservation_user
        FOREIGN KEY(id_user)
        REFERENCES utilisateur(id_user)
        ON DELETE CASCADE,

    CONSTRAINT fk_reservation_trajet
        FOREIGN KEY (id_trajet)
        REFERENCES trajet(id_trajet)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table : avis
CREATE TABLE avis (
    id_avis INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    contenu TEXT NOT NULL,
    statut ENUM('en_attente','valide','rejete') DEFAULT 'en_attente',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    -- Clé étrangère : lien vers utilisateur
    CONSTRAINT fk_avis_user
        FOREIGN KEY (id_user)
        REFERENCES utilisateur(id_user)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table : incident
CREATE TABLE incident (
    id_incident INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    description TEXT NOT NULL,
    statut ENUM('ouvert','en_cours','resolu','rejete') DEFAULT 'ouvert',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    -- Clé étrangère : lien vers utilisateur
    CONSTRAINT fk_incident_user
        FOREIGN KEY (id_user)
        REFERENCES utilisateur(id_user)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
