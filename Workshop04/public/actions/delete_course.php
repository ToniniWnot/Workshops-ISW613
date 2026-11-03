<?php
// Entrada de /actions/delete_course.php: carga el núcleo y llama al controlador.
require_once __DIR__ . '/../../app/bootstrap.php';

(new CourseController())->delete();
