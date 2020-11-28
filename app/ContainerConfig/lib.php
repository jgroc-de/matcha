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
    return new Common(
        $container->get('blacklist'),
        $container->get('friends'),
        $container->get('msg'),
        $container->get('notif'),
        $container->get('tag'),
        $container->get('user'),
        $container->get('mail')
    );
};

$container['debug'] = function () {
    return new Debug();
};

$container['flash'] = new FlashMessage();

$container['mail'] = function ($container) {
    if ($_ENV['PROD']) {
        if ($_ENV['MAILGUN_API_KEY']) {
            $mail = new \App\Lib\Mail\MyMailGun();
        } else {
            $mail = new \App\Lib\Mail\MySendGrid();
        }
    } else {
        $mail = new \App\Lib\Mail\MyPHPMailer();
    }
    return new MailSender($container->get('flash'), $mail, $container->get('settings')['siteUrl']);
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
