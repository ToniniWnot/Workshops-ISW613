<?php
// Entrada de /index.php (página de inicio): carga el núcleo y llama al controlador.
require_once __DIR__ . '/../app/bootstrap.php';

(new AuthController())->showLogin();