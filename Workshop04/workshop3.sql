-- Base de datos del Workshop 3: usuarios, provincias, carreras y cursos.
CREATE DATABASE IF NOT EXISTS workshop3
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE workshop3;

-- Catálogo de provincias (dropdown del registro y del perfil)
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

-- Catálogo de carreras (dropdown del formulario de cursos)
CREATE TABLE IF NOT EXISTS carreras (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_carreras_nombre (nombre)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Cursos: cada curso pertenece a una carrera
CREATE TABLE IF NOT EXISTS cursos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    codigo VARCHAR(20) NOT NULL,
    carrera_id BIGINT UNSIGNED NOT NULL,
    creditos TINYINT UNSIGNED NOT NULL,
    descripcion TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_cursos_codigo (codigo),
    CONSTRAINT fk_cursos_carrera
        FOREIGN KEY (carrera_id) REFERENCES carreras (id),
    CONSTRAINT chk_cursos_creditos
        CHECK (creditos BETWEEN 1 AND 20)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

INSERT IGNORE INTO provincias (nombre) VALUES
    ('San José'), ('Alajuela'), ('Cartago'), ('Heredia'),
    ('Guanacaste'), ('Puntarenas'), ('Limón');

INSERT IGNORE INTO carreras (nombre) VALUES
    ('Ingeniería del Software'),
    ('Tecnologías de Información'),
    ('Administración de Empresas'),
    ('Contaduría');

-- Cursos de ejemplo para que el listado no aparezca vacío
INSERT IGNORE INTO cursos (nombre, codigo, carrera_id, creditos, descripcion) VALUES
    ('Programación Web', 'ISW-613',
        (SELECT id FROM carreras WHERE nombre = 'Ingeniería del Software'), 4,
        'Desarrollo de aplicaciones web del lado del servidor con PHP y MySQL.'),
    ('Bases de Datos', 'ISW-512',
        (SELECT id FROM carreras WHERE nombre = 'Ingeniería del Software'), 4,
        'Modelado relacional, SQL y diseño de bases de datos.'),
    ('Contabilidad General', 'CON-101',
        (SELECT id FROM carreras WHERE nombre = 'Contaduría'), 3,
        'Fundamentos de contabilidad y estados financieros.');