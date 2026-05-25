CREATE DATABASE IF NOT EXISTS car_marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE car_marketplace;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    email VARCHAR(160) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    title VARCHAR(200) NOT NULL,
    category VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    brand VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    year INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    fuel VARCHAR(50) NOT NULL,
    body_type VARCHAR(50) NOT NULL,
    image VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_car (brand, model, year)
);

INSERT IGNORE INTO users (username, email, password, role) VALUES
('admin', 'admin@carmarketplace.local', '$2y$10$1E0s0iFUhMe2DMr339FVMOkrqvwHQJl0Fs1VLFdjqrop7Zxx4/EJO', 'admin');

INSERT IGNORE INTO cars (user_id, brand, model, year, price, fuel, body_type, image, status) VALUES
(NULL, 'Audi', 'RS7', 2023, 85000.00, 'Petrol', 'Sedan', 'img/audi-sedan.png', 'active'),
(NULL, 'Audi', 'R8', 2022, 150000.00, 'Petrol', 'Sport', 'img/audi-sport.jpg', 'active'),
(NULL, 'Audi', 'Q5', 2021, 45000.00, 'Diesel', 'SUV', 'img/audi-suv.jpg', 'active'),

(NULL, 'BMW', '3 Series', 2023, 50000.00, 'Petrol', 'Sedan', 'img/bmw-sedan.jpg', 'active'),
(NULL, 'BMW', 'M4', 2022, 90000.00, 'Petrol', 'Sport', 'img/bmw-sport.jpg', 'active'),
(NULL, 'BMW', 'X5', 2021, 70000.00, 'Diesel', 'SUV', 'img/bmw-suv.jpg', 'active'),

(NULL, 'Mercedes', 'E-Class', 2023, 60000.00, 'Petrol', 'Sedan', 'img/mercedes-sedan.jpg', 'active'),
(NULL, 'Mercedes', 'AMG GT', 2022, 140000.00, 'Petrol', 'Sport', 'img/mercedes-sport.jpg', 'active'),
(NULL, 'Mercedes', 'G-Class', 2023, 130000.00, 'Petrol', 'SUV', 'img/mercedes-suv.jpg', 'active'),

(NULL, 'Tesla', 'Model S', 2023, 90000.00, 'Electric', 'Sedan', 'img/tesla-sedan.jpg', 'active'),
(NULL, 'Tesla', 'Roadster', 2022, 200000.00, 'Electric', 'Sport', 'img/tesla-sport.jpg', 'active'),
(NULL, 'Tesla', 'Model X', 2023, 110000.00, 'Electric', 'SUV', 'img/tesla-suv.jpg', 'active');
