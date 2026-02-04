-- Gestion des réservations d'une bibliothèque

CREATE DATABASE IF NOT EXISTS bibliotheque CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE bibliotheque;



-- 1. Table des utilisateurs/membres

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    inscription DATE NOT NULL,
    role CHAR(5) NOT NULL
)ENGINE=InnoDB;

-- 2. Table des livres
CREATE TABLE books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    published_year YEAR,
    copies_available INT DEFAULT 0
)ENGINE=InnoDB;

-- 3. Table des réservations
CREATE TABLE reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    reservation_date DATE NOT NULL,
    status ENUM('active', 'completed', 'canceled') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
)ENGINE=InnoDB;


