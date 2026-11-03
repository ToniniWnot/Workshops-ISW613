<?php
// Entrada de /actions/register.php: carga el núcleo y llama al controlador.
require_once __DIR__ . '/../../app/bootstrap.php';

(new AuthController())->register();
