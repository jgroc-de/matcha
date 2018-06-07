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
    $this->any('/editProfil', Home::class . ':editProfil')->setName('editProfil');
    $this->any('/editPassword', Home::class . ':editPassword')->setName('editPassword');
    $this->get('/logout', LogIn::class . ':logout')->setName('logout');
    $this->get('/addFriend/{id}', Ajax::class . ':friendRequest');
    $this->post('/addTag', Ajax::class . ':addTag');
    $this->get('/delFriend/{id}', Ajax::class . ':delFriend');
    $this->get('/delUserTag/{id}', Ajax::class . ':delUserTag');
    $this->get('/delPicture/{id}', Ajax::class . ':delPicture');
    $this->get('/delFriendReq/{id}', Ajax::class . ':delFriendRequest');
    $this->post('/updateGeolocation', Ajax::class . ':updateGeolocation');
    $this->get('/tchat/{id}', Ajax::class . ':tchat');
})->add(new \App\Middlewares\authMiddleware());

$app->run();
