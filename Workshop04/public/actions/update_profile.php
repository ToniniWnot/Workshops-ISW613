<?php
// Entrada de /actions/update_profile.php: carga el núcleo y llama al controlador.
require_once __DIR__ . '/../../app/bootstrap.php';

(new ProfileController())->update();
