-- Base de datos del Workshop 2: usuarios asociados a una provincia.
CREATE DATABASE IF NOT EXISTS workshop2
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE workshop2;

-- Catálogo de provincias (alimenta el dropdown del registro)
CREATE TABLE IF NOT EXISTS provincias (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_provincias_nombre (nombre)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Usuarios: guardan el hash de la contraseña (nunca la contraseña) y su provincia
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    provincia_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username),
    CONSTRAINT fk_users_provincia
        FOREIGN KEY (provincia_id) REFERENCES provincias (id)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

INSERT IGNORE INTO provincias (nombre) VALUES
    ('San José'), ('Alajuela'), ('Cartago'), ('Heredia'),
    ('Guanacaste'), ('Puntarenas'), ('Limón');