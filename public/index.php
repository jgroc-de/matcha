<?php
session_start();

require '../vendor/autoload.php';
require '../config/config.php';
use \App\Controllers\RoutesController;

$app = new \Slim\App(['settings' => $config]);

require '../app/container.php';

$app->get('/', RoutesController::class . ':home');
$app->get('/setup', RoutesController::class . ':setup');

$app->get('/login', RoutesController::class . ':home')->setName('login');
$app->post('/login', RoutesController::class . ':login');
$app->get('/logout', RoutesController::class . ':logout');

$app->get('/signup', RoutesController::class . ':signup')->setName('signup');
$app->post('/signup', RoutesController::class . ':signup')->setName('signup');


$app->run();
