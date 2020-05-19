<?php

use App\Lib\CustomError;
use App\Lib\Debug;
use App\Lib\FlashMessage;
use App\Lib\FormChecker;
use App\Lib\ft_geoIP;
use App\Lib\MailSender;
use App\Lib\MyZmq;
use App\Lib\Validator;
use App\Model\BlacklistModel;
use App\Model\FriendsModel;
use App\Model\MessageModel;
use App\Model\NotificationModel;
use App\Model\TagModel;
use App\Model\UserModel;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

$container = $app->getContainer();

$container['view'] = function ($container) {
    $view = new Twig('../app/View', [
        'cache' => false, //'../tmp/cache',
        'debug' => true,
    ]);
    $basePath = rtrim(
        str_ireplace(
            'index.php',
            '',
            $container->get('request')->getUri()->getBasePath()
        ),
        '/'
    );
    $view->addExtension(new TwigExtension($container->get('router'), $basePath));

    return $view;
};

/**
 * database container
 */
$container['db'] = function ($container) {
    $db = $container->get('settings')['db'];
    $pdo = new \PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'], $db['user'], $db['pass']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

    return $pdo;
};

$container['user'] = function ($container) {
    return new UserModel($container->get('db'));
};

$container['friends'] = function ($container) {
    return new FriendsModel(
        $container->get('db'),
        $container->get('MyZMQ'),
        $container->get('Flash'),
        $container->get('user')
    );
};

$container['tag'] = function ($container) {
    return new TagModel($container->get('db'));
};

$container['msg'] = function ($container) {
    return new MessageModel($container->get('db'));
};

$container['notif'] = function ($container) {
    return new NotificationModel($container->get('db'));
};

$container['blacklist'] = function ($container) {
    return new BlacklistModel($container->get('db'));
};


$container['form'] = function ($container) {
    return new FormChecker(
        $container->get('validator'),
        $container->get('flash'),
        $container->get('user'),
        $container->get('mail'),
        $container->get('ft_geoIP')
    );
};

$container['validator'] = function ($container) {
    return new Validator();
};

$container['debug'] = function ($container) {
    return new Debug();
};

$container['flash'] = function () {
    return new FlashMessage();
};

$container['mail'] = function () {
    return new MailSender();
};

$container['notFoundHandler'] = function ($container) {
    return new CustomError($container->get('view'));
};

$container['notAllowedHandler'] = function ($container) {
    return new CustomError($container->get('view'));
};

$container['geoIP'] = function () {
    return new GeoIp2\Database\Reader('../geoIP2/GeoLite2-City_20180501/GeoLite2-City.mmdb');
};

$container['ft_geoIP'] = function ($container) {
    return new ft_geoIP($container->get('validator'), $container->get('geoIP'), $container->get('user'));
};

$container['MyZmq'] = function ($container) {
    return new MyZmq($container->get('zmq'), $container->get('blacklist'), $container->get('notif'));
};

$container['zmq'] = function ($container): ZMQSocket {
    $context = new ZMQContext();
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
    $socket->connect('tcp://localhost:5555');

    return $socket;
};
