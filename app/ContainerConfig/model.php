<?php
/**
 * database container
 */

use App\Model\BlacklistModel;
use App\Model\FriendsModel;
use App\Model\MessageModel;
use App\Model\NotificationModel;
use App\Model\TagModel;
use App\Model\UserModel;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container['db'] = function ($container) {
    try {
        $db = $container->get('settings')['db'];
        $pdo = new \PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'], $db['user'], $db['pass']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    } catch (\PDOException $error) {
        try {
            $pdo = new \PDO('mysql://vyuqi2ey7r0q5q3r:scyksdsel0daocy9@e7qyahb3d90mletd.chr7pe7iynqr.eu-west-1.rds.amazonaws.com:3306/srnidj0gjae99zx7');
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        } catch (\PDOException $error) {
            $pdo = null;
        }
    }

    return $pdo;
};

$container['user'] = function ($container) {
    return new UserModel($container->get('db'));
};

$container['friends'] = function ($container) {
    return new FriendsModel($container->get('db'));
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
