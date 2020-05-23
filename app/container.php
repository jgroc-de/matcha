<?php

use GeoIp2\Database\Reader;
use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

$container = $app->getContainer();

require_once 'ContainerConfig/controller.php';
require_once 'ContainerConfig/lib.php';
require_once 'ContainerConfig/model.php';

$container['view'] = function ($container): Twig {
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
    $view->getEnvironment()->addGlobal('PUB_GG_KEY', $_ENV['PUB_GG_KEY']);
    $view->getEnvironment()->addGlobal('BASE_URL', $container['settings']['siteUrl']);
    $view->addExtension(new TwigExtension($container->get('router'), $basePath));

    return $view;
};

$container['geoIP'] = function (): Reader {
    return new Reader('../geoIP2/GeoLite2-City_20180501/GeoLite2-City.mmdb');
};

$container['zmq'] = function () {
    if (class_exists('ZMQContext')) {
        $context = new ZMQContext();
        $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
        $socket->connect('tcp://localhost:5555');

        return $socket;
    } else {
        return null;
    }
};

$container['curl'] = function (): Client {
    return new Client([
        // You can set any number of default request options.
        'timeout' => 2.0,
    ]);
};

$container['log'] = function () {
    $logger = new Logger('my_logger');
    $file_handler = new StreamHandler('../tmp/logs/app.log');
    $logger->pushHandler($file_handler);

    return $logger;
};
