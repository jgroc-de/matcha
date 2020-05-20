<?php

use Slim\App;
use Slim\Csrf\Guard;

session_start();

require '../vendor/autoload.php';

// Instatiate the app
$config = [
    // Slim settings
    'displayErrorDetails' => true,
    'addContentLengthHeader' => false,
    //global
    'siteUrl' => 'http://localhost:8080',
    // database settings
    'db' => [
        'host' => 'localhost',
        'user' => 'matcha',
        'pass' => 'matcha',
        'dbname' => 'matcha',
    ],
];
$app = new App(['settings' => $config]);
// CSRF
//$app->add(new Guard);

// enabling lazy cors
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', $this->get('settings')['siteUrl'])
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Set up dependencies
require '../app/container.php';

// Register routes
require '../app/routes.php';

// Run!
$app->run();
