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
    $this->get('/profil/{id:[0-9]+}', Profil::class)
        ->setName('profil');
    $this->get('/addFriend/{id:[0-9]+}', AddFriendRequest::class);
    $this->get('/report/{id:[0-9]+}', Report::class);
    $this->get('/blacklist/{id:[0-9]+}', Blacklist::class);
    $this->post('/addPicture/{id:[0-9]+}', AddPicture::class);
    $this->get('/delFriend/{id:[0-9]+}', DeleteFriend::class);
    $this->get('/delUserTag/{id:[0-9]+}', DeleteUserTag::class);
    $this->get('/delPicture/{id:[0-9]+}', DeletePicture::class);
    $this->get('/delFriendReq/{id:[0-9]+}', DeleteFriendRequest::class);
    $this->get('/startChat/{id:[0-9]+}', Chat::class . ':startChat');
    $this->get('/profilStatus/{id:[0-9]+}', Chat::class . ':profilStatus');
})->add(new authMiddleware($container));

/**
 * for cors
 * Catch-all route to serve a 404 Not Found page if none of the routes match
 * NOTE: make sure this route is defined last
 */
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});
