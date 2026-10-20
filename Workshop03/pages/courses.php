<?php
// Listado de cursos: solo accesible con una sesión válida y activa.
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/database.php';
require_once __DIR__ . '/../utils/courses.php';

// Si no hay sesión (o caducó), requireLogin() redirige al login y no se ejecuta nada más
requireLogin();

// Mensajes que pueden enviar las acciones de cursos en los parámetros ?success= y ?error=
$messages = [
    'success' => [
        'created' => 'El curso se creó correctamente.',
        'updated' => 'El curso se actualizó correctamente.',
        'deleted' => 'El curso se eliminó correctamente.',
    ],
    'error' => [
        'invalid_input' => 'La solicitud no es válida.',
        'server_error' => 'No se pudo completar la operación. Inténtalo más tarde.',
    ],
];

// Solo mostramos mensajes de la lista (nunca texto que venga de la URL)
$successCode = $_GET['success'] ?? '';
$errorCode = $_GET['error'] ?? '';
$successMessage = is_string($successCode) ? ($messages['success'][$successCode] ?? null) : null;
$errorMessage = is_string($errorCode) ? ($messages['error'][$errorCode] ?? null) : null;

try {
    $courses = getCourses($conn);
} catch (PDOException $exception) {
    error_log('Courses list error: ' . $exception->getMessage());
    http_response_code(500);
    exit('No se pudieron cargar los cursos. Inténtalo más tarde.');
}

$pageTitle = 'Cursos';
$activePage = 'courses'; // marca "Cursos" como activo en el menú
require_once __DIR__ . '/common/header.php';
?>
<body>
  <?php require_once __DIR__ . '/common/navbar.php'; ?>

  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="m-0">Cursos</h2>
      <a href="/pages/course_form.php" class="btn btn-success">Crear Curso</a>
    </div>

    <?php if ($successMessage !== null): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>
    <?php if ($errorMessage !== null): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead class="thead-dark">
          <tr>
            <th>Nombre</th>
            <th>Código</th>
            <th>Carrera</th>
            <th>Créditos</th>
            <th>Descripción</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($courses) === 0): ?>
            <tr>
              <td colspan="6" class="text-center text-muted">No hay cursos registrados.</td>
            </tr>
          <?php endif; ?>

          <?php foreach ($courses as $course): ?>
            <?php // Todo lo que viene de la base de datos se escapa con htmlspecialchars para evitar XSS ?>
            <tr>
              <td><?php echo htmlspecialchars($course['nombre']); ?></td>
              <td><?php echo htmlspecialchars($course['codigo']); ?></td>
              <td>
                <?php echo htmlspecialchars($course['carrera_nombre']); ?>
                <small class="text-muted">(ID <?php echo (int) $course['carrera_id']; ?>)</small>
              </td>
              <td><?php echo (int) $course['creditos']; ?></td>
              <td><?php echo htmlspecialchars($course['descripcion']); ?></td>
              <td class="text-nowrap">
                <a href="/pages/course_form.php?id=<?php echo (int) $course['id']; ?>" class="btn btn-sm btn-primary">Editar</a>

                <?php // Eliminar usa POST; el mensaje de confirmación es fijo (nunca lleva datos del curso) ?>
                <form action="/actions/delete_course.php" method="POST" class="d-inline"
                      onsubmit="return confirm('¿Seguro que deseas eliminar este curso?');">
                  <input type="hidden" name="id" value="<?php echo (int) $course['id']; ?>">
                  <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php require_once __DIR__ . '/common/footer.php'; ?>