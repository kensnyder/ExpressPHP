<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/lib/Express/Express.php');
require_once('/Users/kensnyder/Documents/www/rightintel/git/master/Code/vendors/ppr.php');

$app = new Express_Server();

$app->get('/hello/:id', function($request, $response, $next) {
	return $next();
	$response->send('Hello World.');
	
});

$app->get('/hello/:id/:hehe', function($request, $response, $next) use($app) {
	if ($request->params['id'] == 1) {
		$response->forward('/hello/2/c');
	}
	$response->send('Hello World 2.');
	ppr($app);
});
$app->listen();
