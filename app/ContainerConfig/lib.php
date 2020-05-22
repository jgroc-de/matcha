<?php

use App\Lib\Common;
use App\Lib\CustomError;
use App\Lib\Debug;
use App\Lib\FlashMessage;
use App\Lib\FormChecker;
use App\Lib\ft_geoIP;
use App\Lib\MailSender;
use App\Lib\MyZmq;
use App\Lib\Validator;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
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
    return new Validator($container->get('flash'));
};

$container['common'] = function ($container) {
    return new Common($container);
};

$container['debug'] = function () {
    return new Debug();
};

$container['flash'] = new FlashMessage();

$container['mail'] = function () {
    return new MailSender();
};

$container['notFoundHandler'] = function ($container) {
    return new CustomError($container->get('view'));
};

$container['notAllowedHandler'] = function ($container) {
    return new CustomError($container->get('view'));
};

$container['ft_geoIP'] = function ($container) {
    return new ft_geoIP($container->get('validator'), $container->get('geoIP'), $container->get('user'));
};

$container['MyZmq'] = function ($container) {
    return new MyZmq($container->get('zmq'), $container->get('blacklist'), $container->get('notif'));
};
