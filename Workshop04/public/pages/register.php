<?php
// Entrada de /pages/register.php: carga el núcleo y llama al controlador.
require_once __DIR__ . '/../../app/bootstrap.php';

(new AuthController())->showRegister();
