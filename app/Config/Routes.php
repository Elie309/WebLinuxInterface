<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Auth
$routes->get('login', 'Auth\AuthController::index');
$routes->post('auth/login', 'Auth\AuthController::login');

// Dashboard routes
$routes->group('dashboard', static function ($routes) {
    $routes->get('/', 'Dashboard\DashboardController::index');
    $routes->get('services', 'Dashboard\DashboardController::services');
    $routes->get('services/(:segment)', 'Dashboard\DashboardController::serviceDetails/$1');
    // ...other existing dashboard routes...
});
