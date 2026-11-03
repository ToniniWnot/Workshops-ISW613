<?php
// Vista del registro: solo HTML. El controlador le entrega $errorMessage y $provincias.
$pageTitle = 'Registro';
require_once __DIR__ . '/../layouts/header.php';
?>
<body>
  <div class="container mt-5">
    <h1 class="text-center">Workshop 4</h1>
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
              <div class="alert alert-danger text-center"><?php echo e($errorMessage); ?></div>
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
                    <option value="<?php echo (int) $provincia['id']; ?>"><?php echo e($provincia['nombre']); ?></option>
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
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>