<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('sports', static function ($routes) {
	$routes->get('/', 'SportController::index');
	$routes->get('create', 'SportController::create');
	$routes->post('store', 'SportController::store');
	$routes->get('edit/(:num)', 'SportController::edit/$1');
	$routes->post('update/(:num)', 'SportController::update/$1');
	$routes->post('delete/(:num)', 'SportController::delete/$1');
});
