<?php
session_start();

require '../vendor/autoload.php';
require '../config/config.php';

$app = new \Slim\App(['settings' => $config]);
require '../app/container.php';

$app->get('/setup', App\Controllers\Initialize::class . ':route')->setName('setup');
$app->get('/seed', App\Controllers\FakeFactory::class . ':route')->setName('seed');

$app->group('', function () {
    $this->any('/login', \App\Controllers\Login::class . ':route')->setName('login');
    $this->any('/signup', App\Controllers\Signup::class . ':route')->setName('signup');
    $this->get('/validation', App\Controllers\Validation::class . ':route')->setName('validation');
    $this->any('/resetPassword', App\Controllers\ResetPassword::class . ':route')->setName('resetPassword');
})->add(new \App\Middlewares\noAuthMiddleware());

$app->group('', function () {
    $this->get('/', App\Controllers\Home::class . ':route')->setName('home');
    $this->get('/search', App\Controllers\Search::class . ':route')->setName('search');
    $this->get('/profil/{id}', App\Controllers\Profil::class . ':route')->setName('profil');
    $this->any('/editProfil', App\Controllers\EditProfil::class . ':route')->setName('editProfil');
    $this->any('/editPassword', App\Controllers\EditPassword::class . ':route')->setName('editPassword');
    $this->get('/logout', App\Controllers\Logout::class . ':route')->setName('logout');
    $this->get('/addFriend/{id}', App\Controllers\AddFriend::class . ':route');
    $this->post('/addTag', App\Controllers\AddTag::class . ':route');
    $this->get('/delFriend/{id}', App\Controllers\DeleteFriend::class . ':route');
    $this->get('/delUserTag/{id}', App\Controllers\DeleteUserTag::class . ':route');
    $this->get('/delPicture/{id}', App\Controllers\DeletePicture::class . ':route');
    $this->get('/delFriendReq/{id}', App\Controllers\DeleteFriendRequest::class . ':route');
    $this->post('/updateGeolocation', App\Controllers\UpdateGeolocation::class . ':route');
    $this->get('/tchat/{id}', App\Controllers\Tchat::class . ':route');
})->add(new \App\Middlewares\authMiddleware());

$app->run();
