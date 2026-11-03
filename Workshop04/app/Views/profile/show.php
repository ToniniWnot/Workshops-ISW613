<?php
// Vista del perfil: solo HTML. El controlador le entrega $currentUsername, $user, $provincias,
// $errorMessage y $updated.
$pageTitle = 'Perfil';
$activePage = 'profile'; // marca "Mi perfil" como activo en el menú
require_once __DIR__ . '/../layouts/header.php';
?>
<body>
  <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>
  <div class="container mt-5">
    <h1 class="text-center">Workshop 4</h1>
    <?php // El nombre sale de la sesión: es lo que guardó actions/login.php al autenticarse ?>
    <p class="text-center">Sesión iniciada como <strong><?php echo e($currentUsername); ?></strong></p>
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
              <div class="alert alert-danger text-center"><?php echo e($errorMessage); ?></div>
            <?php endif; ?>

            <?php // actions/update_profile.php nos devuelve aquí con ?updated=1 si se guardó bien ?>
            <?php if ($updated): ?>
              <div class="alert alert-success text-center">Tus datos se actualizaron correctamente.</div>
            <?php endif; ?>

            <?php // Los datos se envían por POST a actions/update_profile.php ?>
            <form action="/actions/update_profile.php" method="POST">
              <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" class="form-control" id="username" name="username" maxlength="50" value="<?php echo e($user['username']); ?>" required>
              </div>
              <div class="form-group">
                <label for="provincia_id">Provincia</label>
                <select class="form-control" id="provincia_id" name="provincia_id" required>
                  <?php foreach ($provincias as $provincia): ?>
                    <?php // "selected" deja marcada la provincia actual del usuario ?>
                    <option value="<?php echo (int) $provincia['id']; ?>"<?php echo (int) $provincia['id'] === (int) $user['provincia_id'] ? ' selected' : ''; ?>><?php echo e($provincia['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button type="submit" class="btn btn-primary btn-block">Guardar cambios</button>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>