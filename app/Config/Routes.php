<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


$routes->get('/', 'Profil::index');

$routes->get('api/regimes/(:any)', 'Profil::get_regimes_json/$1');
