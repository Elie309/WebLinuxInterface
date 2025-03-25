<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Auth
$routes->get('login', 'Auth\AuthController::index');
$routes->post('auth/login', 'Auth\AuthController::login');
