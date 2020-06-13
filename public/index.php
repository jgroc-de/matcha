<?php

use Slim\App;
use Symfony\Component\Dotenv\Dotenv;

// secure the session cookie in code
session_set_cookie_params([
    'samesite' => 'Strict',
    'HttpOnly' => true,
]);

session_start();

require '../vendor/autoload.php';

// setup $_ENV
$dotenv = new Dotenv();
if (is_file(__DIR__.'/../.env')) {
    $dotenv->load(__DIR__ . '/../.env');
}

// init cloudinary
if ($_ENV['CLOUDINARY_URL']) {
    \Cloudinary::config_from_url($_ENV['CLOUDINARY_URL']);
}

$proto = strpos($_SERVER['HTTP_HOST'], 'localhost') === 0 ? 'http' : 'https';
// Instatiate the app
$app = new App(['settings' => [
    // Slim settings
    'displayErrorDetails' => $_ENV['PROD'] == 0,
    'addContentLengthHeader' => false,
    //global
    'siteUrl' => $proto . '://' . $_SERVER['HTTP_HOST'],
]]);

// Set up dependencies
require '../app/container.php';

// add security headers
require '../app/security.php';

// Register routes
require '../app/routes.php';

// Run!
$app->run();
