<?php
session_start();

require '../vendor/autoload.php';
require '../config/config.php';
use \App\Controllers\HomeController as Home;
use \App\Controllers\SetupController as Setup;

$app = new \Slim\App(['settings' => $config]);
require '../app/container.php';

$app->get('/setup', App\Init::class . ':route')->setName('setup');
$app->get('/seed', App\FakeFactory::class . ':route')->setName('seed');

$app->group('', function () {
    $this->any('/login', App\Login::class . ':route')->setName('login');
    $this->any('/signup', App\Signup::class . ':route')->setName('signup');
    $this->get('/validation', App\Validation::class . ':route')->setName('validation');
    $this->any('/resetPassword', App\ResetPassword::class . ':route')->setName('resetPassword');
})->add(new \App\Middlewares\noAuthMiddleware());

$app->group('', function () {
    $this->get('/', Home::class . ':home')->setName('home');
    $this->get('/search', Home::class . ':search')->setName('search');
    $this->get('/profil/{id}', Home::class . ':profil')->setName('profil');
    $this->any('/editProfil', Home::class . ':editProfil')->setName('editProfil');
    $this->any('/editPassword', Home::class . ':editPassword')->setName('editPassword');
    $this->get('/logout', App\Logout::class . ':route')->setName('logout');
    $this->get('/addFriend/{id}', App\AddFriend::class . ':route');
    $this->post('/addTag', App\AddTag::class . ':route');
    $this->get('/delFriend/{id}', App\DeleteFriend::class . ':route');
    $this->get('/delUserTag/{id}', App\DeleteUserTag::class . ':route');
    $this->get('/delPicture/{id}', App\DeletePicture::class . ':route');
    $this->get('/delFriendReq/{id}', App\DeleteFriendRequest::class . ':route');
    $this->post('/updateGeolocation', App\UpdateGeolocation::class . ':route');
    $this->get('/tchat/{id}', App\Tchat::class . ':route');
})->add(new \App\Middlewares\authMiddleware());

$app->run();
