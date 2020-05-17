<?php

use App\Controllers\AddFriendRequest;
use App\Controllers\AddPicture;
use App\Controllers\AddTag;
use App\Controllers\Blacklist;
use App\Controllers\Chat;
use App\Controllers\Contact;
use App\Controllers\DeleteFriend;
use App\Controllers\DeleteFriendRequest;
use App\Controllers\DeletePicture;
use App\Controllers\DeleteUserTag;
use App\Controllers\EditPassword;
use App\Controllers\EditProfil;
use App\Controllers\FakeFactory;
use App\Controllers\Home;
use App\Controllers\InitializeDB;
use App\Controllers\Login;
use App\Controllers\Logout;
use App\Controllers\Profil;
use App\Controllers\Report;
use App\Controllers\ResetPassword;
use App\Controllers\RGPD;
use App\Controllers\Search;
use App\Controllers\Signup;
use App\Controllers\UpdateGeolocation;
use App\Controllers\Validation;
use App\Middlewares\authMiddleware;
use App\Middlewares\idMiddleware;
use App\Middlewares\noAuthMiddleware;

$app->get('/setup', InitializeDB::class)
    ->setName('setup');
$app->get('/seed', FakeFactory::class)
    ->setName('seed');
$app->get('/contact', Contact::class)
    ->setName('contact');
$app->post('/contact', Contact::class . ':sendMail');
$app->get('/validation', Validation::class)
    ->setName('validation');

$app->group('', function () {
    $this->get('/login', Login::class)
        ->setName('login');
    $this->get('/signup', Signup::class)
        ->setName('signup');
    $this->get('/resetPassword', ResetPassword::class)
        ->setName('resetPassword');
    $this->post('/login', Login::class . ':check');
    $this->post('/signup', Signup::class . ':check');
    $this->post('/resetPassword', ResetPassword::class . ':check');
})->add(new noAuthMiddleware());

$app->group('', function () use ($app) {
    $this->get('/', Home::class)
        ->setName('home');
    $this->get('/search', Search::class)
        ->setName('search');
    $this->post('/search_criteria', Search::class . ':criteria')
        ->setName('searchByCriteria');
    $this->post('/search_user', Search::class . ':name')
        ->setName('searchByName');
    $this->get('/editProfil', EditProfil::class)
        ->setName('editProfil');
    $this->post('/editProfil', EditProfil::class . ':check');
    $this->get('/completeProfil', EditProfil::class . ':complete')
        ->setName('editProfil2');
    $this->get('/editPassword', EditPassword::class)
        ->setName('editPassword');
    $this->post('/editPassword', EditPassword::class . ':check');
    $this->get('/rgpd', RGPD::class)
        ->setName('RGPD');
    $this->get('/getAllDatas', RGPD::class . ':getAllDatas');
    $this->get('/deleteAccount', RGPD::class . ':deleteAccount')
        ->setName('deleteAccount');
    $this->get('/logout', Logout::class)
        ->setName('logout');
    $this->get('/tchat', Chat::class)
        ->setName('tchat');
    $this->get('/chatStatus', Chat::class . ':mateStatus');
    $this->post('/updateGeolocation', UpdateGeolocation::class);
    $this->post('/sendMessage', Chat::class . ':send');
    $this->post('/addTag', AddTag::class);
})->add(new authMiddleware($container));

$app->group('', function () {
    $this->get('/profil/{id}', Profil::class)
        ->setName('profil');
    $this->get('/addFriend/{id}', AddFriendRequest::class);
    $this->get('/report/{id}', Report::class);
    $this->get('/blacklist/{id}', Blacklist::class);
    $this->post('/addPicture/{id}', AddPicture::class);
    $this->get('/delFriend/{id}', DeleteFriend::class);
    $this->get('/delUserTag/{id}', DeleteUserTag::class);
    $this->get('/delPicture/{id}', DeletePicture::class);
    $this->get('/delFriendReq/{id}', DeleteFriendRequest::class);
    $this->get('/startChat/{id}', Chat::class . ':startChat');
    $this->get('/profilStatus/{id}', Chat::class . ':profilStatus');
})
    ->add(new idMiddleware())
    ->add(new authMiddleware($container));
