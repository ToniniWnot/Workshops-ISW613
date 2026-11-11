<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('users', 'Users::index');
$routes->get('users/new', 'Users::create');
$routes->post('users', 'Users::store');
$routes->get('users/(:num)/edit', 'Users::edit/$1');
$routes->post('users/(:num)/update', 'Users::update/$1');
$routes->post('users/(:num)/delete', 'Users::delete/$1');