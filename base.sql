DROP DATABASE IF EXISTS Regime;

DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS parametres;
DROP TABLE IF EXISTS regimes;
DROP TABLE IF EXISTS prix_regimes;
DROP TABLE IF EXISTS activites_sportives;
DROP TABLE IF EXISTS activites_objectifs;
DROP TABLE IF EXISTS categories_imc;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS codes_portefeuille;
DROP TABLE IF EXISTS souscriptions;
DROP TABLE IF EXISTS paiements;

CREATE DATABASE Regime;
USE Regime;

-- 1. ADMINISTRATION & CONFIGURATION
-- ------------------------------------------------------------
DROP TABLE IF EXISTS admins;
CREATE TABLE admins (
    id INT PRIMARY KEY,
    nom VARCHAR(100),
    email VARCHAR(100),
    mot_de_passe VARCHAR(255),
    role VARCHAR(20)
);

INSERT INTO admins (id, nom, email, mot_de_passe, role) VALUES 
(1, 'Rakoto Jean', 'admin@regimeapp.mg', 'Admin@2026', 'superadmin'),
(2, 'Rasoa Marie', 'manager@regimeapp.mg', 'Manager@2026', 'manager');

DROP TABLE IF EXISTS parametres;
CREATE TABLE parametres (
    id INT PRIMARY KEY,
    cle VARCHAR(50),
    valeur VARCHAR(50),
    description TEXT,
    updated_at DATETIME
);

INSERT INTO parametres (id, cle, valeur, description, updated_at) VALUES 
(1, 'imc_min_normal', '18.5', 'IMC minimum pour être considéré normal', NOW()),
(2, 'imc_max_normal', '24.9', 'IMC maximum pour être considéré normal', NOW()),
(3, 'prix_option_gold', '250000', 'Prix option Gold en Ariary (paiement unique)', NOW()),
(4, 'remise_gold', '15', 'Pourcentage de remise pour les membres Gold', NOW()),
(5, 'proteines_jour_g', '500', 'Apport protéines de base par jour en grammes', NOW());


-- 2. RÉGIMES ET TARIFICATION
-- ------------------------------------------------------------

DROP TABLE IF EXISTS objectifs;
CREATE TABLE objectifs (
    id INT PRIMARY KEY,
    nom VARCHAR(100),
    description TEXT
);

INSERT INTO objectifs VALUES 
(1, 'reduction', 'Perte de poids rapide et efficace.'),
(2, 'augmentation', 'Prise de masse musculaire.'),
(3, 'equilibre', 'Maintien du poids et équilibre alimentaire.');

-- CATÉGORIES IMC (pour l'objectif "équilibre")
DROP TABLE IF EXISTS categories_imc;
CREATE TABLE categories_imc (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100),
    imc_min FLOAT,
    imc_max FLOAT,
    description TEXT,
    ordre INT
);

INSERT INTO categories_imc (nom, imc_min, imc_max, description, ordre) VALUES 
('Maigreur', 0, 18.4, 'IMC inférieur à 18.5 - Poids insuffisant', 1),
('Normal', 18.5, 24.9, 'IMC entre 18.5 et 24.9 - Poids normal', 2),
('Surpoids', 25, 29.9, 'IMC entre 25 et 29.9 - Surpoids', 3),
('Obésité légère', 30, 34.9, 'IMC entre 30 et 34.9 - Obésité légère', 4),
('Obésité modérée', 35, 39.9, 'IMC entre 35 et 39.9 - Obésité modérée', 5),
('Obésité sévère', 40, 100, 'IMC 40 et plus - Obésité sévère', 6);

DROP TABLE IF EXISTS regimes;
CREATE TABLE regimes (
    id INT PRIMARY KEY,
    nom VARCHAR(100),
    description TEXT,
    pourcentage_viande INT,
    pourcentage_poisson INT,
    pourcentage_volaille INT,
    variation_poids FLOAT,
    duree_semaines INT,
    id_objectif INT,
    actif TINYINT(1),
    FOREIGN KEY (id_objectif) REFERENCES objectifs(id)
);

INSERT INTO regimes VALUES 
(1, 'Régime Méditerranéen', 'Riche en poisson, faible en viande rouge.', 10, 60, 30, -0.5, 8, 1, 1),
(2, 'Régime Hyperprotéiné', 'Fort apport pour la masse musculaire.', 50, 10, 40, 0.8, 12, 2, 1),
(3, 'Régime Équilibré Classique', 'Répartition harmonieuse des protéines.', 30, 40, 30, -0.2, 10, 3, 1),
(4, 'Régime Détox Poisson', 'Purification et perte rapide.', 5, 80, 15, -0.7, 6, 1, 1),
(5, 'Régime Masse Volaille', 'Prise de masse sans graisses saturées.', 20, 20, 60, 0.6, 16, 2, 1);

DROP TABLE IF EXISTS prix_regimes;
CREATE TABLE prix_regimes (
    id INT PRIMARY KEY,
    regime_id INT,
    duree_semaines INT,
    prix DECIMAL(10,2),
    FOREIGN KEY (regime_id) REFERENCES regimes(id)
);

INSERT INTO prix_regimes VALUES 
(1, 1, 4, 35000), (2, 1, 8, 60000), (3, 1, 12, 85000),
(4, 2, 4, 40000), (5, 2, 8, 70000), (6, 2, 12, 95000),
(7, 3, 4, 30000), (8, 3, 8, 55000), (9, 3, 12, 75000),
(10, 4, 4, 38000), (11, 4, 6, 52000),
(12, 5, 8, 65000), (13, 5, 16, 110000);


-- 3. ACTIVITÉS SPORTIVES
-- ------------------------------------------------------------
DROP TABLE IF EXISTS activites_sportives;
CREATE TABLE activites_sportives (
    id INT PRIMARY KEY,
    nom VARCHAR(100),
    description TEXT,
    calories_par_heure INT,
    actif TINYINT(1)
);

INSERT INTO activites_sportives VALUES 
(1, 'Course à pied', 'Jogging intensité modérée.', 550, 1),
(2, 'Natation', 'Nage libre, excellent pour les articulations.', 500, 1),
(3, 'Vélo', 'Cyclisme extérieur ou stationnaire.', 480, 1),
(4, 'Musculation', 'Entraînement avec poids.', 350, 1),
(5, 'Yoga / Stretching', 'Souplesse et récupération.', 200, 1);

-- RELATION ACTIVITÉS SPORTIVES - OBJECTIFS
DROP TABLE IF EXISTS activites_objectifs;
CREATE TABLE activites_objectifs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    activite_id INT,
    objectif_id INT,
    niveau_priorite INT COMMENT '1=haute, 2=moyenne, 3=basse',
    FOREIGN KEY (activite_id) REFERENCES activites_sportives(id),
    FOREIGN KEY (objectif_id) REFERENCES objectifs(id)
);

INSERT INTO activites_objectifs (activite_id, objectif_id, niveau_priorite) VALUES 
-- Réduction (perte de poids)
(1, 1, 1), -- Course à pied - très efficace pour brûler calories
(2, 1, 1), -- Natation - sans impact sur les articulations
(3, 1, 2), -- Vélo - cardio modéré
(5, 1, 3), -- Yoga - aide à récupération
-- Augmentation (prise de masse)
(4, 2, 1), -- Musculation - priorité haute
(3, 2, 2), -- Vélo - cardio pour endurance
(5, 2, 2), -- Yoga - flexibilité et récupération
(1, 2, 3), -- Course à pied - cardio léger
-- Équilibre (maintien/IMC idéal)
(5, 3, 1), -- Yoga - excellent pour équilibre
(1, 3, 2), -- Course à pied - entretien cardio
(2, 3, 2), -- Natation - sport complet
(3, 3, 2), -- Vélo - activité équilibrée
(4, 3, 2); -- Musculation - tonification générale

DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(100),
    mot_de_passe VARCHAR(255),
    genre CHAR(1),
    taille INT,
    poids FLOAT,
    imc FLOAT,
    solde_portefeuille DECIMAL(10,2),
    option_gold TINYINT(1),
    created_at DATETIME
);

INSERT INTO users (id, nom, prenom, email, mot_de_passe, genre, taille, poids, imc, solde_portefeuille, option_gold, created_at)VALUES 
(1, 'Andriantsoa', 'Rakia', 'rakia@gmail.com', 'Rakia@123','F', 160, 75.0, 29.3, 10000, 0, '2026-01-10 08:30:00'),
(2, 'Rakotoarison', 'Feno', 'feno@gmail.com', 'Feno@123','M', 175, 55.0, 18.0, 25000, 1, '2026-01-15 10:00:00'),
(3, 'Razafindra', 'Hery', 'hery@gmail.com', 'Hery@123','M', 178, 90.0, 28.4, 5000, 0, '2026-02-01 09:15:00'),
(4, 'Rasamimanana', 'Miora', 'miora@gmail.com', 'Miora@123','F', 165, 62.0, 22.8, 15000, 1, '2026-02-20 14:00:00'),
(5, 'Razanakoto', 'Dada', 'dada@gmail.com', 'Dada@123','M', 180, 48.0, 14.8, 3000, 0, '2026-03-05 11:45:00');

DROP TABLE IF EXISTS codes_portefeuille;
CREATE TABLE codes_portefeuille (
    id INT PRIMARY KEY,
    code VARCHAR(50),
    montant DECIMAL(10,2),
    est_valide TINYINT(1),
    created_at DATETIME
);

INSERT INTO codes_portefeuille (id, code, montant, est_valide, created_at) VALUES 
(1, 'BIENV-100K-2026', 100000, 0, NOW()),
(2, 'PROMO-5K-MARS', 5000, 0, NOW()),
(3, 'BONUS-20K-VIP', 20000, 0, NOW()),
(4, 'CODE-15K-SANTE', 15000, 0, NOW()),
(7, 'START-3K-NEW', 3000, 0, NOW()),
(8, 'OFFRE-250K-GOLD', 250000, 0, NOW()),
(9, 'SUPER-500K-2026', 500000, 0, NOW()),
(10, 'EXTRA-1M-REGIME', 1000000, 0, NOW());




DROP TABLE IF EXISTS souscriptions;
CREATE TABLE souscriptions (
    id INT PRIMARY KEY,
    user_id INT,
    regime_id INT,
    prix_regime_id INT,
    objectif_choisi VARCHAR(50),
    date_debut DATE,
    date_fin DATE,
    montant_paye DECIMAL(10,2),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (regime_id) REFERENCES regimes(id),
    FOREIGN KEY (prix_regime_id) REFERENCES prix_regimes(id)
);

INSERT INTO souscriptions VALUES 
(1, 1, 1, 2, 'reduction', '2026-01-20', '2026-03-16', 60000),
(2, 2, 2, 6, 'augmentation', '2026-01-25', '2026-04-19', 80750),
(3, 3, 3, 8, 'equilibre', '2026-02-10', '2026-04-06', 55000),
(4, 4, 3, 9, 'equilibre', '2026-03-01', '2026-05-24', 63750),
(5, 5, 5, 12, 'augmentation', '2026-03-10', '2026-05-05', 65000);


CREATE TABLE type_paiments (
    id INT PRIMARY KEY,
    nom VARCHAR(50)
);
DROP TABLE IF EXISTS paiements;
CREATE TABLE paiements (
    id INT PRIMARY KEY,
    user_id INT,
    souscription_id INT NULL,
    montant DECIMAL(10,2),
    id_type_paiement INT,
    created_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (souscription_id) REFERENCES souscriptions(id),
    FOREIGN KEY (id_type_paiement) REFERENCES type_paiments(id)
);

INSERT INTO type_paiments VALUES 
(1, 'regime'),
(2, 'gold');

INSERT INTO paiements VALUES 
(1, 1, 1, 60000, 1, '2026-01-20 08:00:00'),
(2, 2, NULL, 250000, 2, '2026-01-15 10:30:00'),
(3, 2, 2, 80750, 1, '2026-01-25 11:00:00'),
(4, 3, 3, 55000, 1, '2026-02-10 09:00:00'),
(5, 4, NULL, 250000, 2, '2026-02-20 14:30:00'),
(6, 4, 4, 63750, 1, '2026-03-01 14:00:00'),
(7, 5, 5, 65000, 1, '2026-03-10 11:00:00');

