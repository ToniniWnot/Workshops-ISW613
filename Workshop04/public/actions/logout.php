<?php
// Entrada de /actions/logout.php: carga el núcleo y llama al controlador.
require_once __DIR__ . '/../../app/bootstrap.php';

(new AuthController())->logout();
