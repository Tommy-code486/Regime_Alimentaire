<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::authenticate');
$routes->get('register', 'Auth::registerStep1');
$routes->post('register/step1', 'Auth::storeStep1');
$routes->get('register/step2', 'Auth::registerStep2');
$routes->post('register/step2', 'Auth::storeStep2');
$routes->get('logout', 'Auth::logout');

$routes->get('dashboard', 'Dashboard::index');
$routes->post('dashboard/objectif', 'Dashboard::updateObjectif');
$routes->post('dashboard/imc-target', 'Dashboard::updateIMCTarget');
$routes->get('admin/dashboard', 'Dashboard::admin');
$routes->get('option-gold', 'Dashboard::gold');
$routes->post('option-gold/activer', 'Gold::activate');
$routes->get('regimes-suggeres', 'Dashboard::regimes');
$routes->post('regimes-suggeres/choisir', 'RegimeSubscription::subscribe');
$routes->post('portefeuille/valider', 'Portefeuille::validationCode');
$routes->get('regimes-liste', 'Regimes::showRegimesList');
$routes->get('regimes/create', 'Regimes::create');
$routes->post('regimes', 'Regimes::store');
$routes->get('regimes/(:num)/edit', 'Regimes::edit/$1');
$routes->post('regimes/(:num)/update', 'Regimes::update/$1');
$routes->post('regimes/(:num)/delete', 'Regimes::delete/$1');
// Admin statistics
$routes->get('admin/stats', 'AdminStats::index');

// Sports CRUD (admin only via controller)
$routes->group('sports', static function ($routes) {
	$routes->get('/', 'SportController::index');
	$routes->get('create', 'SportController::create');
	$routes->post('store', 'SportController::store');
	$routes->get('edit/(:num)', 'SportController::edit/$1');
	$routes->post('update/(:num)', 'SportController::update/$1');
	$routes->post('delete/(:num)', 'SportController::delete/$1');
});
