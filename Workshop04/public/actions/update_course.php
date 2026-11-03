<?php
// Entrada de /actions/update_course.php: carga el núcleo y llama al controlador.
require_once __DIR__ . '/../../app/bootstrap.php';

(new CourseController())->update();
