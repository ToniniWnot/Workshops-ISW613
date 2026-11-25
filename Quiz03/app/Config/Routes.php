<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
// Matrícula (página principal)
$routes->get('/', 'Enrollments::index');
$routes->post('enrollments', 'Enrollments::store');
$routes->get('enrollments/(:num)', 'Enrollments::show/$1');

// CRUD de cursos
$routes->get('courses', 'Courses::index');
$routes->get('courses/new', 'Courses::create');
$routes->post('courses', 'Courses::store');
$routes->get('courses/(:num)/edit', 'Courses::edit/$1');
$routes->post('courses/(:num)/update', 'Courses::update/$1');
$routes->post('courses/(:num)/delete', 'Courses::delete/$1');
