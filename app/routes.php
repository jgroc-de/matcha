<?php

// Routes

$app->get('/setup', App\Controllers\InitializeDB::class)
    ->setName('setup');
$app->get('/seed', App\Controllers\FakeFactory::class)
    ->setName('seed');
$app->get('/contact', App\Controllers\Contact::class)
    ->setName('contact');
$app->post('/contact', App\Controllers\Contact::class . ':sendMail');
$app->get('/validation', App\Controllers\Validation::class)
    ->setName('validation');

$app->group('', function () {
    $this->get('/login', App\Controllers\Login::class)
        ->setName('login');
    $this->get('/signup', App\Controllers\Signup::class)
        ->setName('signup');
    $this->get('/resetPassword', App\Controllers\ResetPassword::class)
        ->setName('resetPassword');
    $this->post('/login', App\Controllers\Login::class . ':check');
    $this->post('/signup', App\Controllers\Signup::class . ':check');
    $this->post('/resetPassword', App\Controllers\ResetPassword::class . ':check');
})->add(new \App\Middlewares\noAuthMiddleware());

$app->group('', function () use ($app) {
    $this->get('/', App\Controllers\Home::class)
        ->setName('home');
    $this->get('/search', App\Controllers\Search::class)
        ->setName('search');
    $this->post('/search_criteria', App\Controllers\Search::class . ':criteria')
        ->setName('searchByCriteria');
    $this->post('/search_user', App\Controllers\Search::class . ':name')
        ->setName('searchByName');
    $this->get('/editProfil', App\Controllers\EditProfil::class)
        ->setName('editProfil');
    $this->post('/editProfil', App\Controllers\EditProfil::class . ':check');
    $this->get('/completeProfil', App\Controllers\EditProfil::class . ':complete')
        ->setName('editProfil2');
    $this->get('/editPassword', App\Controllers\EditPassword::class)
        ->setName('editPassword');
    $this->post('/editPassword', App\Controllers\EditPassword::class . ':check');
    $this->get('/rgpd', App\Controllers\RGPD::class)
        ->setName('RGPD');
    $this->get('/getAllDatas', App\Controllers\RGPD::class . ':getAllDatas');
    $this->get('/deleteAccount', App\Controllers\RGPD::class . ':deleteAccount')
        ->setName('deleteAccount');
    $this->get('/logout', App\Controllers\Logout::class)
        ->setName('logout');
    $this->get('/tchat', App\Controllers\Chat::class)
        ->setName('tchat');
    $this->get('/chatStatus', App\Controllers\Chat::class . ':mateStatus');
    $this->post('/updateGeolocation', App\Controllers\UpdateGeolocation::class);
    $this->post('/sendMessage', App\Controllers\Chat::class . ':send');
    $this->post('/addTag', App\Controllers\AddTag::class);
})->add(new \App\Middlewares\authMiddleware($container));

$app->group('', function () {
    $this->get('/profil/{id}', App\Controllers\Profil::class)
        ->setName('profil');
    $this->get('/addFriend/{id}', App\Controllers\AddFriendRequest::class);
    $this->get('/report/{id}', App\Controllers\Report::class);
    $this->get('/blacklist/{id}', App\Controllers\Blacklist::class);
    $this->post('/addPicture/{id}', App\Controllers\AddPicture::class);
    $this->get('/delFriend/{id}', App\Controllers\DeleteFriend::class);
    $this->get('/delUserTag/{id}', App\Controllers\DeleteUserTag::class);
    $this->get('/delPicture/{id}', App\Controllers\DeletePicture::class);
    $this->get('/delFriendReq/{id}', App\Controllers\DeleteFriendRequest::class);
    $this->get('/startChat/{id}', App\Controllers\Chat::class . ':startChat');
    $this->get('/profilStatus/{id}', App\Controllers\Chat::class . ':profilStatus');
})
    ->add(new \App\Middlewares\idMiddleware($container))
    ->add(new \App\Middlewares\authMiddleware($container));
