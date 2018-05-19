<?php
session_start();

require '../vendor/autoload.php';
require '../config/config.php';
use \App\Controllers\RoutesController;
use \App\Setup;

$app = new \Slim\App(['settings' => $config]);
require '../app/container.php';

$app->get('/setup', Setup::class . ':init')->setName('setup');
$app->get('/seed', Setup::class . ':fakeFactory')->setName('seed');

$app->any('/login', RoutesController::class . ':login')->setName('login');
$app->get('/logout', RoutesController::class . ':logout')->setName('logout');
$app->any('/signup', RoutesController::class . ':signup')->setName('signup');
$app->get('/validation', RoutesController::class . ':validation')->setName('validation');
$app->any('/reInitPassword', RoutesController::class . ':reInitPassword')->setName('reInitPassword');

$app->get('/', RoutesController::class . ':home')->setName('home');
$app->any('/profil', RoutesController::class . ':profil')->setName('profil');
$app->any('/password', RoutesController::class . ':password')->setName('password');

$app->run();
