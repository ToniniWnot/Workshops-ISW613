<?php
// Perfil del usuario: solo accesible con una sesión válida y activa.
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/database.php';

// Si no hay sesión (o caducó), requireLogin() redirige al login y no se ejecuta nada más
requireLogin();

// Mensajes de error que puede enviar actions/update_profile.php en el parámetro ?error=
$errorMessages = [
    'invalid_input' => 'Datos inválidos. El username puede tener hasta 50 caracteres y debes elegir una provincia.',
    'username_taken' => 'Ese username ya está en uso. Elige otro.',
    'server_error' => 'No se pudieron guardar los cambios. Inténtalo más tarde.',
];
$errorCode = $_GET['error'] ?? '';
$errorMessage = is_string($errorCode) ? ($errorMessages[$errorCode] ?? null) : null;

try {
    // Datos actuales del usuario (identificado por el id guardado en la sesión) y lista de provincias
    $statement = $conn->prepare('SELECT username, provincia_id FROM users WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $_SESSION['user_id']]);
    $user = $statement->fetch();
    $provincias = $conn->query('SELECT id, nombre FROM provincias ORDER BY nombre')->fetchAll();
} catch (PDOException $exception) {
    error_log('Profile database error: ' . $exception->getMessage());
    http_response_code(500);
    exit('No se pudo cargar tu perfil. Inténtalo más tarde.');
}

// Si el usuario ya no existe en la base de datos, la sesión deja de ser válida
if ($user === false) {
    destroySession();
    header('Location: /index.php');
    exit;
}

$pageTitle = 'Perfil';
require_once __DIR__ . '/common/header.php';
?>
<body>
  <div class="container mt-5">
    <h1 class="text-center">Workshop 2</h1>
    <?php // El nombre sale de la sesión: es lo que guardó actions/login.php al autenticarse ?>
    <p class="text-center">Sesión iniciada como <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
  </div>

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header text-center">
            <h4>Mi perfil</h4>
          </div>
          <div class="card-body">
            <?php if ($errorMessage !== null): ?>
              <div class="alert alert-danger text-center"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <?php // actions/update_profile.php nos devuelve aquí con ?updated=1 si se guardó bien ?>
            <?php if (isset($_GET['updated'])): ?>
              <div class="alert alert-success text-center">Tus datos se actualizaron correctamente.</div>
            <?php endif; ?>

            <?php // Los datos se envían por POST a actions/update_profile.php ?>
            <form action="/actions/update_profile.php" method="POST">
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" maxlength="50" value="<?php echo htmlspecialchars($user['username']); ?>" required>
              </div>
              <div class="form-group">
                <label for="provincia_id">Provincia</label>
                <select class="form-control" id="provincia_id" name="provincia_id" required>
                  <?php foreach ($provincias as $provincia): ?>
                    <?php // "selected" deja marcada la provincia actual del usuario ?>
                    <option value="<?php echo (int) $provincia['id']; ?>"<?php echo (int) $provincia['id'] === (int) $user['provincia_id'] ? ' selected' : ''; ?>><?php echo htmlspecialchars($provincia['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button type="submit" class="btn btn-primary btn-block">Guardar cambios</button>
            </form>

            <?php // El logout también va por POST para que no se pueda disparar con solo abrir un enlace ?>
            <form action="/actions/logout.php" method="POST" class="mt-3">
              <button type="submit" class="btn btn-danger btn-block">Cerrar sesión</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php require_once __DIR__ . '/common/footer.php'; ?>