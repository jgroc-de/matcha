<?php

use App\Controllers\Authentication;
use App\Controllers\Blacklist;
use App\Controllers\Chat;
use App\Controllers\Contact;
use App\Controllers\FriendRequest;
use App\Controllers\Geolocation;
use App\Controllers\Picture;
use App\Controllers\Profil;
use App\Controllers\RGPD;
use App\Controllers\Search;
use App\Controllers\Settings;
use App\Controllers\Setup;
use App\Controllers\Tag;
use App\Middlewares\authMiddleware;
use App\Middlewares\noAuthMiddleware;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Slim\Http\Request;
use Slim\Http\Response;

// enabling lazy cors
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', $this->get('settings')['siteUrl'])
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

/** @var $app Slim\App */
$app->get('/setup', Setup::class . ':initDB')
    ->setName('setup');
$app->get('/seed', Setup::class . ':seed')
    ->setName('seed');
$app->get('/phpinfo', Setup::class . ':phpInfo');
$app->get('/memcached', Setup::class . ':memcached');
$app->get('/contact', Contact::class . ':page')
    ->setName('contact');
$app->post('/contact', Contact::class . ':mail');
$app->get('/validation', RGPD::class . ':validationDeletion')
    ->setName('validation');

$app->group('', function () {
    $this->get('/login', Authentication::class . ':login')
        ->setName('login');
    $this->map(['GET', 'POST'], '/apiLogin/{name:42|google}', Authentication::class . ':apiLogin')
        ->setName('apiLogin');
    $this->post('/login', Authentication::class . ':postLogin');
    $this->get('/signup', Authentication::class . ':signup')
        ->setName('signup');
    $this->post('/signup', Authentication::class . ':postSignup');
    $this->get('/resetPassword', Authentication::class . ':resetPassword')
        ->setName('resetPassword');
    $this->post('/resetPassword', Authentication::class . ':postPassword');
})->add(new noAuthMiddleware());

$app->group('', function () use ($app) {
    $this->get('/', Profil::class . ':page')
        ->setName('home');
    $this->get('/profil/{id:[0-9]+}', Profil::class . ':profil')
        ->setName('profil');
    $this->put('/updateGeolocation', Geolocation::class);

    $this->get('/search', Search::class . ':main')
        ->setName('search');
    $this->post('/search-criteria', Search::class . ':criteria')
        ->setName('searchByCriteria');
    $this->post('/search-name', Search::class . ':name')
        ->setName('searchByName');

    $this->get('/editProfil', Settings::class . ':editProfil')
        ->setName('editProfil');
    $this->post('/editProfil', Settings::class . ':updateProfil');
    $this->get('/editPassword', Settings::class . ':editPassword')
        ->setName('editPassword');
    $this->post('/editPassword', Settings::class . ':updatePassword');
    $this->get('/rgpd', Settings::class . ':rgpd')
        ->setName('RGPD');

    $this->post('/getAllData', RGPD::class . ':getAllData')
        ->setName('getMyData');
    $this->post('/deleteAccount', RGPD::class . ':deleteAccount')
        ->setName('deleteAccount');

    $this->get('/logout', Authentication::class . ':logout')
        ->setName('logout');

    $this->get('/tchat', Chat::class . ':page')
        ->setName('tchat');
    $this->get('/chatStatus', Chat::class . ':mateStatus');
    $this->post('/sendMessage', Chat::class . ':send');
    $this->get('/startChat/{id:[0-9]+}', Chat::class . ':startChat');
    $this->get('/profilStatus/{id:[0-9]+}', Chat::class . ':profilStatus');

    $this->post('/tag', Tag::class . ':add');
    $this->delete('/tag/{id:[0-9]+}', Tag::class . ':delete');

    $this->post('/friend/{id:[0-9]+}', FriendRequest::class . ':add');
    $this->delete('/friend/{id:[0-9]+}', FriendRequest::class . ':delete');
    $this->delete('/friendReq/{id:[0-9]+}', FriendRequest::class . ':deleteRequest');

    $this->post('/report/{id:[0-9]+}', Blacklist::class . ':report');
    $this->post('/blacklist/{id:[0-9]+}', Blacklist::class . ':add');

    $this->post('/picture/{id:[0-9]+}', Picture::class . ':add');
    $this->delete('/picture/{id:[0-9]+}', Picture::class . ':delete');
})->add(new authMiddleware($container));

/**
 * for cors
 * Catch-all route to serve a 404 Not Found page if none of the routes match
 * NOTE: make sure this route is defined last
 */
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

/** logger */
$app->add(function (Request $request, Response $response, $next) {
    $response = $next($request, $response);
    $logger = new Logger('server');
    $file_handler = new StreamHandler('../tmp/logs/app.log');
    $logger->pushHandler($file_handler);
    $error = $response->getStatusCode();
    $method = $request->getMethod();
    $id = $_SESSION['id'] ?? 0 ;
    $user = $_SESSION['profil']['pseudo'] ?? '';
    $server = $request->getServerParams();
    $ip = $server['REMOTE_ADDR'];
    $uri = $server['REQUEST_URI'];
    $logger->info("[$error] $method - $uri - id: $id - user: $user - IP: $ip");

    return $response;
});
