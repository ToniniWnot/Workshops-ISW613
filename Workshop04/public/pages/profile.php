<?php
// Entrada de /pages/profile.php: carga el núcleo y llama al controlador.
require_once __DIR__ . '/../../app/bootstrap.php';

(new ProfileController())->show();
