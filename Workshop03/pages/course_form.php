<?php
// Formulario de cursos: sirve para crear (sin id en la URL) y para editar (?id=...).
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/database.php';
require_once __DIR__ . '/../utils/courses.php';
require_once __DIR__ . '/../utils/careers.php';

// Si no hay sesión (o caducó), requireLogin() redirige al login y no se ejecuta nada más
requireLogin();

// Mensajes de error que pueden enviar las acciones en el parámetro ?error=
$errorMessages = [
    'invalid_input' => 'Datos inválidos. Revisa que todos los campos estén completos y dentro de sus límites.',
    'code_taken' => 'Ese código de curso ya existe. Usa otro.',
    'invalid_career' => 'Selecciona una carrera válida.',
    'server_error' => 'No se pudo guardar el curso. Inténtalo más tarde.',
];
$errorCode = $_GET['error'] ?? '';
$errorMessage = is_string($errorCode) ? ($errorMessages[$errorCode] ?? null) : null;

try {
    $course = null;

    // Con ?id=... estamos editando: validamos el id y cargamos el curso
    if (isset($_GET['id'])) {
        $id = is_string($_GET['id'])
            ? filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : false;

        if ($id === false) {
            header('Location: /pages/courses.php?error=invalid_input');
            exit;
        }

        $course = findCourse($conn, $id);

        if ($course === null) {
            header('Location: /pages/courses.php?error=not_found');
            exit;
        }
    }

    $careers = getCareers($conn);
} catch (PDOException $exception) {
    error_log('Course form error: ' . $exception->getMessage());
    http_response_code(500);
    exit('No se pudo cargar el formulario. Inténtalo más tarde.');
}

// Un solo formulario, dos modos: según haya o no un curso cargado cambian el título y la acción
$isEdit = $course !== null;
$formTitle = $isEdit ? 'Editar curso' : 'Crear curso';
$formAction = $isEdit ? '/actions/update_course.php' : '/actions/create_course.php';

$pageTitle = $formTitle;
$activePage = 'courses'; // marca "Cursos" como activo en el menú
require_once __DIR__ . '/common/header.php';
?>
<body>
  <?php require_once __DIR__ . '/common/navbar.php'; ?>

  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header text-center">
            <h4><?php echo htmlspecialchars($formTitle); ?></h4>
          </div>
          <div class="card-body">
            <?php if ($errorMessage !== null): ?>
              <div class="alert alert-danger text-center"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <?php // Los datos se envían por POST a la acción de crear o de editar ?>
            <form action="<?php echo $formAction; ?>" method="POST">
              <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?php echo (int) $course['id']; ?>">
              <?php endif; ?>

              <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100"
                       value="<?php echo htmlspecialchars($course['nombre'] ?? ''); ?>" required>
              </div>

              <div class="form-group">
                <label for="codigo">Código de curso</label>
                <input type="text" class="form-control" id="codigo" name="codigo" maxlength="20"
                       pattern="[A-Za-z0-9\-]+" title="Solo letras, números y guiones"
                       value="<?php echo htmlspecialchars($course['codigo'] ?? ''); ?>" required>
              </div>

              <div class="form-group">
                <label for="carrera_id">Carrera</label>
                <select class="form-control" id="carrera_id" name="carrera_id" required>
                  <?php if (!$isEdit): ?>
                    <option value="" disabled selected>Selecciona una carrera</option>
                  <?php endif; ?>
                  <?php foreach ($careers as $career): ?>
                    <?php // "selected" deja marcada la carrera actual del curso al editar ?>
                    <option value="<?php echo (int) $career['id']; ?>"<?php echo $isEdit && (int) $career['id'] === (int) $course['carrera_id'] ? ' selected' : ''; ?>><?php echo htmlspecialchars($career['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label for="creditos">Créditos</label>
                <input type="number" class="form-control" id="creditos" name="creditos" min="1" max="20"
                       value="<?php echo isset($course) ? (int) $course['creditos'] : ''; ?>" required>
              </div>

              <div class="form-group">
                <label for="descripcion">Descripción del curso</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" maxlength="1000" required><?php echo htmlspecialchars($course['descripcion'] ?? ''); ?></textarea>
              </div>

              <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Guardar cambios' : 'Crear curso'; ?></button>
              <a href="/pages/courses.php" class="btn btn-secondary">Cancelar</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php require_once __DIR__ . '/common/footer.php'; ?>