<?php
// Procesa el formulario de "Crear curso": valida, guarda y redirige al listado.
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/database.php';
require_once __DIR__ . '/../utils/courses.php';

// Solo usuarios con sesión válida y activa pueden llegar más allá de esta línea
requireLogin();

// Solo se acepta POST; si alguien abre esta URL directamente, lo mandamos al listado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/courses.php');
    exit;
}

// Validamos y limpiamos los datos; si algo no es válido, volvemos al formulario con un error
$data = validateCourseInput($_POST);

if ($data === null) {
    header('Location: /pages/course_form.php?error=invalid_input');
    exit;
}

try {
    createCourse($conn, $data);
} catch (PDOException $exception) {
    // courseErrorCode() distingue código repetido, carrera inexistente y error del servidor
    header('Location: /pages/course_form.php?error=' . courseErrorCode($exception));
    exit;
}

// Curso creado: volvemos al listado de cursos con un mensaje de éxito
header('Location: /pages/courses.php?success=created');
exit;