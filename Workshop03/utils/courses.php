<?php
// Funciones de datos de los cursos. Reciben la conexión PDO y usan consultas preparadas.

// Devuelve todos los cursos con el nombre de su carrera, ordenados por código
function getCourses(PDO $conn): array
{
    return $conn->query(
        'SELECT c.id, c.nombre, c.codigo, c.carrera_id, ca.nombre AS carrera_nombre,
                c.creditos, c.descripcion
         FROM cursos c
         JOIN carreras ca ON ca.id = c.carrera_id
         ORDER BY c.codigo'
    )->fetchAll();
}

// Busca un curso por id. Devuelve null si no existe.
function findCourse(PDO $conn, int $id): ?array
{
    $statement = $conn->prepare(
        'SELECT id, nombre, codigo, carrera_id, creditos, descripcion
         FROM cursos WHERE id = :id LIMIT 1'
    );
    $statement->execute(['id' => $id]);

    return $statement->fetch() ?: null;
}

// Inserta un curso nuevo y devuelve su id. $data viene de validateCourseInput().
function createCourse(PDO $conn, array $data): int
{
    $statement = $conn->prepare(
        'INSERT INTO cursos (nombre, codigo, carrera_id, creditos, descripcion)
         VALUES (:nombre, :codigo, :carrera_id, :creditos, :descripcion)'
    );
    $statement->execute($data);

    return (int) $conn->lastInsertId();
}

// Actualiza todos los campos editables de un curso. $data viene de validateCourseInput().
function updateCourse(PDO $conn, int $id, array $data): void
{
    $statement = $conn->prepare(
        'UPDATE cursos
         SET nombre = :nombre, codigo = :codigo, carrera_id = :carrera_id,
             creditos = :creditos, descripcion = :descripcion
         WHERE id = :id'
    );
    $statement->execute($data + ['id' => $id]);
}

// Elimina un curso de forma definitiva
function deleteCourse(PDO $conn, int $id): void
{
    $statement = $conn->prepare('DELETE FROM cursos WHERE id = :id');
    $statement->execute(['id' => $id]);
}

// Valida los datos del formulario. Devuelve los datos limpios o null si algo no es válido.
function validateCourseInput(array $input): ?array
{
    $nombre = $input['nombre'] ?? null;
    $codigo = $input['codigo'] ?? null;
    $carreraId = $input['carrera_id'] ?? null;
    $creditos = $input['creditos'] ?? null;
    $descripcion = $input['descripcion'] ?? null;

    // Todos deben ser texto (un atacante podría enviar arreglos)
    foreach ([$nombre, $codigo, $carreraId, $creditos, $descripcion] as $value) {
        if (!is_string($value)) {
            return null;
        }
    }

    $nombre = trim($nombre);
    $codigo = strtoupper(trim($codigo));
    $descripcion = trim($descripcion);
    $carreraId = filter_var($carreraId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $creditos = filter_var($creditos, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 20]]);

    if (
        $nombre === '' || mb_strlen($nombre) > 100
        || !preg_match('/^[A-Z0-9-]{1,20}$/', $codigo)
        || $carreraId === false
        || $creditos === false
        || $descripcion === '' || mb_strlen($descripcion) > 1000
    ) {
        return null;
    }

    return [
        'nombre' => $nombre,
        'codigo' => $codigo,
        'carrera_id' => $carreraId,
        'creditos' => $creditos,
        'descripcion' => $descripcion,
    ];
}

// Traduce un error de MySQL al código de error que mostrará la página
function courseErrorCode(PDOException $exception): string
{
    $mysqlCode = $exception->errorInfo[1] ?? 0;

    if ($mysqlCode === 1062) { // UNIQUE: el código de curso ya existe
        return 'code_taken';
    }

    if ($mysqlCode === 1452) { // FOREIGN KEY: la carrera no existe
        return 'invalid_career';
    }

    error_log('Course database error: ' . $exception->getMessage());
    return 'server_error';
}