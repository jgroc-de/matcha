<?php
session_start();

require '../vendor/autoload.php';
require '../config/config.php';
use \App\Controllers\RoutesHomeController as Home;
use \App\Controllers\RoutesLogInController as LogIn;
use \App\Controllers\SetupController as Setup;

$app = new \Slim\App(['settings' => $config]);
require '../app/container.php';

$app->get('/setup', Setup::class . ':init')->setName('setup');
$app->get('/seed', Setup::class . ':fakeFactory')->setName('seed');

$app->any('/login', LogIn::class . ':login')->setName('login');
$app->get('/logout', LogIn::class . ':logout')->setName('logout');
$app->any('/signup', LogIn::class . ':signup')->setName('signup');
$app->get('/validation', LogIn::class . ':validation')->setName('validation');
$app->any('/resetPassword', LogIn::class . ':resetPassword')->setName('resetPassword');

$app->group('', function () {
    $this->get('/', Home::class . ':home')->setName('home');
    $this->get('/search', Home::class . ':search')->setName('search');
    $this->get('/profil/{id}', Home::class . ':profil')->setName('profil/{id}');
    $this->any('/profil', Home::class . ':editProfil')->setName('editProfil');
    $this->any('/password', Home::class . ':editPassword')->setName('editPassword');
})->add(new \App\Middlewares\authMiddleware());

$app->run();
