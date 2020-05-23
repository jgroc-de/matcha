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
        $pdo = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
        //$pdo = new \PDO($_ENV['JAWSDB_URL']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    } catch (\PDOException $error) {
        echo $error->getMessage();
        echo "server database fail"; exit();
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
