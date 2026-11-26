-- Creación de la base de datos (si no existe)
CREATE DATABASE IF NOT EXISTS `sistema_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sistema_db`;

-- 1. Tabla de Administradores / Usuarios del sistema
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(120) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabla de Clientes
CREATE TABLE IF NOT EXISTS `clientes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(120) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Administrador de prueba inicial (Clave: 123456)
INSERT INTO `users` (`name`, `email`, `password`) VALUES
('Administrador', 'admin@correo.com', '$2y$10$0Q3z8gq3w3iZg3B7U2fweO3l0.FmswU6HhXy6F4m6V3.8lV6kKx0K')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Cliente de prueba inicial (Clave: 123456)
INSERT INTO `clientes` (`name`, `email`, `password`) VALUES
('Cliente Demo', 'cliente@correo.com', '$2y$10$0Q3z8gq3w3iZg3B7U2fweO3l0.FmswU6HhXy6F4m6V3.8lV6kKx0K')
ON DUPLICATE KEY UPDATE `id`=`id`;
