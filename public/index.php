<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Slim\App;
use Slim\Csrf\Guard;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Dotenv\Dotenv;

session_start();

require '../vendor/autoload.php';

$dotenv = new Dotenv();
if (is_file(__DIR__.'/../.env')) {
    $dotenv->load(__DIR__ . '/../.env');
}
if ($_ENV['CLOUDINARY_URL']) {
    \Cloudinary::config_from_url($_ENV['CLOUDINARY_URL']);
}

$proto = strpos($_SERVER['HTTP_HOST'], 'localhost') === 0 ? 'http' : 'https';
// Instatiate the app
$config = [
    // Slim settings
    'displayErrorDetails' => $_ENV['PROD'],
    'addContentLengthHeader' => false,
    //global
    'siteUrl' => $proto . '://' . $_SERVER['HTTP_HOST'],
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

$app->add(function (Request $request, Response $response, $next) {
    $response = $next($request, $response);
    $logger = new Logger('server');
    $file_handler = new StreamHandler('../tmp/logs/app.log');
    $logger->pushHandler($file_handler);
    $error = $response->getStatusCode();
    $method = $request->getMethod();
    $id = $_SESSION['id'] ?? 0 ;
    $user = $_SESSION['profil']['pseudo'] ?? '';
    $server = $request->getServerParams();
    $ip = $server['REMOTE_ADDR'];
    $uri = $server['REQUEST_URI'];
    $logger->info("[$error] $method - $uri - id: $id - user: $user - IP: $ip");

    return $response;
});

// Run!
$app->run();
