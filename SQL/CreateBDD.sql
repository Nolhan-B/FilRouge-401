CREATE DATABASE IF NOT EXISTS criticlick;
USE criticlick;

-- Suppression de l'utilisateur s'il existe
-- DROP USER IF EXISTS 'db_user'@'localhost';

-- Création de l'utilisateur
CREATE USER IF NOT EXISTS 'db_user'@'localhost' IDENTIFIED BY 'rootmdp'; 

-- Attribution des privilèges à l'utilisateur
GRANT ALL PRIVILEGES ON criticlick.* TO 'db_user'@'localhost';

-- Suppression des tables dans le bon ordre pour éviter les erreurs liées aux clés étrangères
DROP TABLE IF EXISTS avis;
DROP TABLE IF EXISTS article_tag;
DROP TABLE IF EXISTS article;
DROP TABLE IF EXISTS tag;
DROP TABLE IF EXISTS categorie;
DROP TABLE IF EXISTS utilisateur;

-- Création des tables
CREATE TABLE IF NOT EXISTS utilisateur (
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(128) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categorie (
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    nom VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    filtreTag VARCHAR(50),
    published BOOLEAN DEFAULT FALSE,
    nbrProduits INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS article (
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    nom VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    categorie_id INTEGER NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    published BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (categorie_id) REFERENCES categorie(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tag (
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS article_tag (
    article_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES article(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS avis (
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    article_id INT NOT NULL,
    note INT NOT NULL,
    commentaire TEXT NOT NULL,
    lien_achat VARCHAR(255),
    prix DECIMAL(10, 2),
    date_avis TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (article_id) REFERENCES article(id) ON DELETE CASCADE
);