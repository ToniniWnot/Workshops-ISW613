<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <style>
    body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    }
  </style>
</head>
<body>
  <h2>Login</h2>

  <?php if (isset($_GET['error'])): ?>
    <p style="color: red;">Credenciales inválidas</p>
  <?php endif; ?>


  <form action="login.php" method="POST">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required>

    <br><br>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <br><br>

    <button type="submit">Login</button>
  </form>
</body>
</html>