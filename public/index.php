<?php
session_start();

require '../vendor/autoload.php';
require '../config/config.php';
use \App\Controllers\HomeController as Home;
use \App\Controllers\LogInController as LogIn;
use \App\Controllers\SetupController as Setup;
use \App\Controllers\AjaxController as Ajax;

$app = new \Slim\App(['settings' => $config]);
require '../app/container.php';

$app->get('/setup', Setup::class . ':init')->setName('setup');
$app->get('/seed', Setup::class . ':fakeFactory')->setName('seed');

$app->group('', function () {
    $this->any('/login', LogIn::class . ':login')->setName('login');
    $this->any('/signup', LogIn::class . ':signup')->setName('signup');
    $this->get('/validation', LogIn::class . ':validation')->setName('validation');
    $this->any('/resetPassword', LogIn::class . ':resetPassword')->setName('resetPassword');
})->add(new \App\Middlewares\noAuthMiddleware());

$app->group('', function () {
    $this->get('/', Home::class . ':home')->setName('home');
    $this->get('/search', Home::class . ':search')->setName('search');
    $this->get('/profil/{id}', Home::class . ':profil')->setName('profil');
    $this->get('/addFriend/{id}', Ajax::class . ':friendRequest')->setName('addFriend');
    $this->get('/chat/{id}', Ajax::class . ':chat')->setName('chat');
    $this->any('/editProfil', Home::class . ':editProfil')->setName('editProfil');
    $this->any('/editPassword', Home::class . ':editPassword')->setName('editPassword');
    $this->get('/logout', LogIn::class . ':logout')->setName('logout');

})->add(new \App\Middlewares\authMiddleware());

$app->run();
