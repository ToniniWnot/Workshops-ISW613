<?php
// Procesa el botón "Eliminar" del listado: elimina el curso indicado y vuelve al listado.
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/database.php';
require_once __DIR__ . '/../utils/courses.php';

// Solo usuarios con sesión válida y activa pueden llegar más allá de esta línea
requireLogin();

// Eliminar solo funciona por POST: abrir esta URL desde un enlace o la barra de direcciones no borra nada
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

try {
    // Si el curso ya no existe (por ejemplo, un doble clic), avisamos en vez de decir que se eliminó
    if (findCourse($conn, $id) === null) {
        header('Location: /pages/courses.php?error=not_found');
        exit;
    }

    deleteCourse($conn, $id);
} catch (PDOException $exception) {
    error_log('Delete course error: ' . $exception->getMessage());
    header('Location: /pages/courses.php?error=server_error');
    exit;
}

// Curso eliminado: volvemos al listado de cursos con un mensaje de éxito
header('Location: /pages/courses.php?success=deleted');
exit;