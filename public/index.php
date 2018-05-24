<?php
session_start();

require '../vendor/autoload.php';
require '../config/config.php';
use \App\Controllers\RoutesHomeController;
use \App\Controllers\RoutesLogInController;
use \App\Setup;

$app = new \Slim\App(['settings' => $config]);
require '../app/container.php';

$app->get('/setup', Setup::class . ':init')->setName('setup');
$app->get('/seed', Setup::class . ':fakeFactory')->setName('seed');

$app->any('/login', RoutesLogInController::class . ':login')->setName('login');
$app->get('/logout', RoutesLogInController::class . ':logout')->setName('logout');
$app->any('/signup', RoutesLogInController::class . ':signup')->setName('signup');
$app->get('/validation', RoutesLogInController::class . ':validation')->setName('validation');
$app->any('/resetPassword', RoutesLogInController::class . ':resetPassword')->setName('resetPassword');

$app->get('/', RoutesHomeController::class . ':home')->setName('home')->add(new \App\Middlewares\authMiddleware());
$app->get('/search', RoutesHomeController::class . ':search')->setName('search')->add(new \App\Middlewares\authMiddleware());
$app->get('/profil/{id}', RoutesHomeController::class . ':profil')->setName('profil/{id}')->add(new \App\Middlewares\authMiddleware());
$app->any('/profil', RoutesHomeController::class . ':editProfil')->setName('editProfil')->add(new \App\Middlewares\authMiddleware());
$app->any('/password', RoutesHomeController::class . ':editPassword')->setName('editPassword')->add(new \App\Middlewares\authMiddleware());

$app->run();
