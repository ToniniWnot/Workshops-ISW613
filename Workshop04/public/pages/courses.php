<?php
// Entrada de /pages/courses.php: carga el núcleo y llama al controlador.
require_once __DIR__ . '/../../app/bootstrap.php';

(new CourseController())->index();
