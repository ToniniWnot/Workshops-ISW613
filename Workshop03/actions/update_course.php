<?php
// Procesa el formulario de "Editar curso": valida, actualiza y redirige al listado.
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

// El id del curso llega en un campo oculto del formulario: debe ser un entero positivo
$id = isset($_POST['id']) && is_string($_POST['id'])
    ? filter_var($_POST['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
    : false;

if ($id === false) {
    header('Location: /pages/courses.php?error=invalid_input');
    exit;
}

// Validamos y limpiamos los datos; si algo no es válido, volvemos al formulario de ese curso
$data = validateCourseInput($_POST);

if ($data === null) {
    header('Location: /pages/course_form.php?id=' . $id . '&error=invalid_input');
    exit;
}

try {
    // Un UPDATE sobre un id inexistente no falla, así que comprobamos antes que el curso exista
    if (findCourse($conn, $id) === null) {
        header('Location: /pages/courses.php?error=not_found');
        exit;
    }

    updateCourse($conn, $id, $data);
} catch (PDOException $exception) {
    // courseErrorCode() distingue código repetido, carrera inexistente y error del servidor
    header('Location: /pages/course_form.php?id=' . $id . '&error=' . courseErrorCode($exception));
    exit;
}

// Curso actualizado: volvemos al listado de cursos con un mensaje de éxito
header('Location: /pages/courses.php?success=updated');
exit;