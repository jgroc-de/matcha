<?php

use GeoIp2\Database\Reader;
use GuzzleHttp\Client;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;


$container = $app->getContainer();

require_once 'ContainerConfig/controller.php';
require_once 'ContainerConfig/lib.php';
require_once 'ContainerConfig/model.php';

$container['view'] = function($container): Twig {
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

$container['geoIP'] = function(): Reader {
    return new Reader('../geoIP2/GeoLite2-City_20180501/GeoLite2-City.mmdb');
};

$container['zmq'] = function(): ZMQSocket {
    $context = new ZMQContext();
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
    $socket->connect('tcp://localhost:5555');

    return $socket;
};

$container['curl'] = function(): Client {
    return new Client([
        // You can set any number of default request options.
        'timeout'  => 2.0,
    ]);
};
