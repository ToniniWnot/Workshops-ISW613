<?php
// Menú de navegación para las páginas con sesión.
// Cada página define $activePage ('profile' o 'courses') antes de incluir este archivo.
$activePage = $activePage ?? '';
?>
<nav class="navbar navbar-expand navbar-dark bg-dark">
  <span class="navbar-brand">Workshop 3</span>

  <ul class="navbar-nav mr-auto">
    <li class="nav-item<?php echo $activePage === 'profile' ? ' active' : ''; ?>">
      <a class="nav-link" href="/pages/profile.php">Mi perfil</a>
    </li>
    <li class="nav-item<?php echo $activePage === 'courses' ? ' active' : ''; ?>">
      <a class="nav-link" href="/pages/courses.php">Cursos</a>
    </li>
  </ul>

  <?php // El logout va por POST para que no se pueda disparar con solo abrir un enlace ?>
  <form action="/actions/logout.php" method="POST" class="form-inline m-0">
    <button type="submit" class="btn btn-outline-light btn-sm">Cerrar sesión</button>
  </form>
</nav>