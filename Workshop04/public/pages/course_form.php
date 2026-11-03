<?php
// Entrada de /pages/course_form.php: carga el núcleo y llama al controlador.
require_once __DIR__ . '/../../app/bootstrap.php';

(new CourseController())->form();
