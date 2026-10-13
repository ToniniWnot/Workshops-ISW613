<?php
// Pantalla de login: es la página de inicio del sitio.
$pageTitle = 'Iniciar sesión';
require_once __DIR__ . '/pages/common/header.php';
?>
<body>
  <div class="container mt-5">
    <h1 class="text-center">Workshop 2</h1>
    <p class="text-center">Inicia sesión para ver tu perfil.</p>
  </div>

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header text-center">
            <h4>Login</h4>
          </div>
          <div class="card-body">
            <?php // actions/login.php nos devuelve aquí con ?error=1 si algo falla ?>
            <?php if (isset($_GET['error'])): ?>
              <div class="alert alert-danger text-center">Credenciales inválidas</div>
            <?php endif; ?>

            <?php // actions/register.php nos devuelve aquí con ?registered=1 si el registro salió bien ?>
            <?php if (isset($_GET['registered'])): ?>
              <div class="alert alert-success text-center">Registro exitoso. Ya puedes iniciar sesión.</div>
            <?php endif; ?>

            <?php // utils/auth.php nos devuelve aquí con ?expired=1 si la sesión caducó por inactividad ?>
            <?php if (isset($_GET['expired'])): ?>
              <div class="alert alert-warning text-center">Tu sesión expiró por inactividad. Inicia sesión de nuevo.</div>
            <?php endif; ?>

            <?php // Los datos se envían por POST a actions/login.php (la contraseña no va en la URL) ?>
            <form action="actions/login.php" method="POST">
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" maxlength="50" required>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <p class="text-center mt-3 mb-0">¿No tienes cuenta? <a href="pages/register.php">Regístrate</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php require_once __DIR__ . '/pages/common/footer.php'; ?>