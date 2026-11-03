<?php
// Entrada de /actions/create_course.php: carga el núcleo y llama al controlador.
require_once __DIR__ . '/../../app/bootstrap.php';

(new CourseController())->create();
