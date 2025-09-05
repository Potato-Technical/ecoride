-- ECORIDE - structure.sql
-- Script de création de la base MySQL

-- Sécurité : suppression si déjà existant
DROP TABLE IF EXISTS reservation;
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
    role ENUM('passager', 'conducteur', 'employe', 'admin') DEFAULT 'passager'
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
    prix DECIMAL(5,2) NOT NULL,

    -- Clé étrangère : lien vers utilisateur
    CONSTRAINT fk_trajet_conducteur
        FOREIGN KEY (id_conducteur)
        REFERENCES utilisateur(id_user)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table : reservation
CREATE TABLE reservation (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_trajet INT NOT NULL,
    date_reservation DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'confirmée','annulée') DEFAULT 'en_attente',

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
