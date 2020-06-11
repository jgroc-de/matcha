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
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */

$container['App\Controllers\Authentication'] = function ($container) {
    return new Authentication(
        $container,
        $container->get('flash'),
        $container->get('form'),
        $container->get('curl'),
        $container->get('user'),
        $container->get('view')
    );
};

$container['App\Controllers\Blacklist'] = function ($container) {
    return new Blacklist(
        $container->get('blacklist'),
        $container->get('friends'),
        $container->get('user'),
        $container->get('mail')
    );
};

$container['App\Controllers\Chat'] = function($container) {
    return new Chat(
        $container->get('blacklist'),
        $container->get('friends'),
        $container->get('msg'),
        $container->get('MyZmq'),
        $container->get('notif'),
        $container->get('user'),
        $container->get('validator'),
        $container->get('view')
    );
};

$container['App\Controllers\Contact'] = function ($container) {
    return new Contact(
        $container->get('view'),
        $container['flash'],
        $container->get('form')
    );
};

$container['App\Controllers\FriendRequest'] = function ($container) {
    return new FriendRequest(
        $container->get('blacklist'),
        $container->get('friends'),
        $container->get('user'),
        $container->get('MyZmq')
    );
};

$container['App\Controllers\Geolocation'] = function ($container) {
    return new Geolocation(
        $container->get('validator'),
        $container->get('user')
    );
};

$container['App\Controllers\Picture'] = function ($container) {
    return new Picture($container->get('user'));
};

$container['App\Controllers\Profil'] = function ($container) {
    return new Profil(
        $container->get('blacklist'),
        $container->get('notFoundHandler'),
        $container->get('friends'),
        $container->get('MyZmq'),
        $container->get('notif'),
        $container->get('tag'),
        $container->get('view'),
        $container->get('user')
    );
};

$container['App\Controllers\RGPD'] = function ($container) {
    return new RGPD(
        $container->get('common'),
        $container->get('flash'),
        $container->get('mail'),
        $container->get('user'),
        $container->get('validator')
    );
};


$container['App\Controllers\Search'] = function($container) {
    return new Search(
        $container['flash'],
        $container->get('notif'),
        $container->get('tag'),
        $container->get('user'),
        $container->get('validator'),
        $container->get('view')
    );
};

$container['App\Controllers\Settings'] = function ($container) {
    return new Settings(
        $container['flash'],
        $container->get('form'),
        $container->get('mail'),
        $container->get('notif'),
        $container->get('user'),
        $container->get('validator'),
        $container->get('view')
    );
};

$container['App\Controllers\Setup'] = function ($container) {
    return new Setup(
        $container->get('form'),
        $container->get('tag'),
        $container->get('user'),
        $container->get('db')
    );
};

$container['App\Controllers\Tag'] = function ($container) {
    return new Tag($container->get('tag'), $container->get('validator'));
};
