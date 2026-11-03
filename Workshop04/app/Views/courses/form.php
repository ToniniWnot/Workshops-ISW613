<?php
// Vista del formulario de cursos: sirve para crear ($course es null) y para editar.
// El controlador le entrega $course, $careers y $errorMessage.

// Un solo formulario, dos modos: según haya o no un curso cargado cambian el título y la acción
$isEdit = $course !== null;
$formTitle = $isEdit ? 'Editar curso' : 'Crear curso';
$formAction = $isEdit ? '/actions/update_course.php' : '/actions/create_course.php';

$pageTitle = $formTitle;
$activePage = 'courses'; // marca "Cursos" como activo en el menú
require_once __DIR__ . '/../layouts/header.php';
?>
<body>
  <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header text-center">
            <h4><?php echo e($formTitle); ?></h4>
          </div>
          <div class="card-body">
            <?php if ($errorMessage !== null): ?>
              <div class="alert alert-danger text-center"><?php echo e($errorMessage); ?></div>
            <?php endif; ?>

            <?php // Los datos se envían por POST a la acción de crear o de editar ?>
            <form action="<?php echo $formAction; ?>" method="POST">
              <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?php echo (int) $course['id']; ?>">
              <?php endif; ?>

              <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100"
                       value="<?php echo e($course['nombre'] ?? ''); ?>" required>
              </div>

              <div class="form-group">
                <label for="codigo">Código de curso</label>
                <input type="text" class="form-control" id="codigo" name="codigo" maxlength="20"
                       pattern="[A-Za-z0-9\-]+" title="Solo letras, números y guiones"
                       value="<?php echo e($course['codigo'] ?? ''); ?>" required>
              </div>

              <div class="form-group">
                <label for="carrera_id">Carrera</label>
                <select class="form-control" id="carrera_id" name="carrera_id" required>
                  <?php if (!$isEdit): ?>
                    <option value="" disabled selected>Selecciona una carrera</option>
                  <?php endif; ?>
                  <?php foreach ($careers as $career): ?>
                    <?php // "selected" deja marcada la carrera actual del curso al editar ?>
                    <option value="<?php echo (int) $career['id']; ?>"<?php echo $isEdit && (int) $career['id'] === (int) $course['carrera_id'] ? ' selected' : ''; ?>><?php echo e($career['nombre']); ?></option>
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
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" maxlength="1000" required><?php echo e($course['descripcion'] ?? ''); ?></textarea>
              </div>

              <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Guardar cambios' : 'Crear curso'; ?></button>
              <a href="/pages/courses.php" class="btn btn-secondary">Cancelar</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>