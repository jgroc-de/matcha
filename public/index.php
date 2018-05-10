<?php
session_start();

require '../vendor/autoload.php';
require '../config/config.php';
use \App\Controllers\PagesController;

$app = new \Slim\App(['settings' => $config]);

require '../app/container.php';

$app->get('/', PagesController::class . ':home');
$app->get('/login', PagesController::class . ':home')->setName('login');
$app->post('/login', PagesController::class . ':login');
$app->get('/signup', PagesController::class . ':signup')->setName('signup');
$app->post('/signup', PagesController::class . ':signup');

$app->run();
