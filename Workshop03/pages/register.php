<?php
// Pantalla de registro: formulario con username, contraseña y provincia.
require_once __DIR__ . '/../utils/database.php';

// Mensajes de error que puede enviar actions/register.php en el parámetro ?error=
$errorMessages = [
    'invalid_input' => 'Datos inválidos. El username puede tener hasta 50 caracteres y la contraseña entre 8 y 72.',
    'username_taken' => 'Ese username ya está en uso. Elige otro.',
    'server_error' => 'No se pudo completar el registro. Inténtalo más tarde.',
];
$errorCode = $_GET['error'] ?? '';
// Solo mostramos mensajes de la lista (nunca texto que venga de la URL) y descartamos valores que no sean texto
$errorMessage = is_string($errorCode) ? ($errorMessages[$errorCode] ?? null) : null;

// Provincias que llenan el dropdown
try {
    $provincias = $conn->query('SELECT id, nombre FROM provincias ORDER BY nombre')->fetchAll();
} catch (PDOException $exception) {
    error_log('Register provinces error: ' . $exception->getMessage());
    $provincias = [];
    $errorMessage = $errorMessage ?? 'No se pudieron cargar las provincias. Inténtalo más tarde.';
}

$pageTitle = 'Registro';
require_once __DIR__ . '/common/header.php';
?>
<body>
  <div class="container mt-5">
    <h1 class="text-center">Workshop 2</h1>
    <p class="text-center">Crea tu cuenta para continuar.</p>
  </div>

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header text-center">
            <h4>Registro</h4>
          </div>
          <div class="card-body">
            <?php if ($errorMessage !== null): ?>
              <div class="alert alert-danger text-center"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <?php // Los datos se envían por POST a actions/register.php ?>
            <form action="/actions/register.php" method="POST">
              <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" class="form-control" id="username" name="username" maxlength="50" required>
              </div>
              <div class="form-group">
                <label for="password">Contraseña (mínimo 8 caracteres)</label>
                <input type="password" class="form-control" id="password" name="password" minlength="8" maxlength="72" autocomplete="new-password" required>
              </div>
              <div class="form-group">
                <label for="provincia_id">Provincia</label>
                <?php // Cada opción envía el id de la provincia; el usuario ve el nombre ?>
                <select class="form-control" id="provincia_id" name="provincia_id" required>
                  <option value="" disabled selected>Selecciona tu provincia</option>
                  <?php foreach ($provincias as $provincia): ?>
                    <option value="<?php echo (int) $provincia['id']; ?>"><?php echo htmlspecialchars($provincia['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button type="submit" class="btn btn-primary btn-block">Registrarme</button>
            </form>

            <p class="text-center mt-3 mb-0">¿Ya tienes cuenta? <a href="/index.php">Inicia sesión</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php require_once __DIR__ . '/common/footer.php'; ?>