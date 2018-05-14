<?php
session_start();

require '../vendor/autoload.php';
require '../config/config.php';
use \App\Controllers\RoutesController;

$app = new \Slim\App(['settings' => $config]);
$faker = Faker\Factory::create();
require '../app/container.php';
$app->get('/setup', RoutesController::class . ':setup');

$app->get('/', RoutesController::class . ':home')->setName('home');
$app->any('/login', RoutesController::class . ':login')->setName('login');
$app->get('/logout', RoutesController::class . ':logout')->setName('logout');
$app->any('/signup', RoutesController::class . ':signup')->setName('signup');
$app->any('/profil', RoutesController::class . ':profil')->setName('profil');
$app->any('/password', RoutesController::class . ':password')->setName('password');

$app->run();
