<?php

use App\Controllers\ApiController;
use App\Controllers\File_manager;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('Home\Controllers');

$routes->set404Override(function () {
	return view('errors/404');
});
$routes->setAutoRoute(true);

$routes->post('upload_files', [File_manager::class, 'upload_files']);
$routes->post('upload_files_tiny', [File_manager::class, 'upload_files_tiny']);
$routes->get('file-manager/view_files/(:any)', [File_manager::class, 'view_files']);

$routes->group('/', static function ($routes) {
	$routes->get('cron', [ApiController::class, 'cron']);
	$routes->get('api', [ApiController::class, 'index']);
	$routes->match(['get', 'post'], 'api/device-connect', [ApiController::class, 'deviceConnect']);
	$routes->match(['get', 'post'], 'api/add-data', [ApiController::class, 'addMessage']);
});
